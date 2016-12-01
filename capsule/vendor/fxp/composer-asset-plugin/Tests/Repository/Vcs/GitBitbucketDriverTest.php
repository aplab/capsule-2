<?php

/*
 * This file is part of the Fxp Composer Asset Plugin package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Composer\AssetPlugin\Tests\Repository\Vcs;

use Composer\Downloader\TransportException;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Composer\Config;
use Composer\Util\RemoteFilesystem;
use Fxp\Composer\AssetPlugin\Repository\Vcs\GitBitbucketDriver;

/**
 * Tests of vcs git bitbucket repository.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class GitBitbucketDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config();
        $this->config->merge(array(
            'config' => array(
                'home' => sys_get_temp_dir().'/composer-test',
                'cache-repo-dir' => sys_get_temp_dir().'/composer-test-cache',
            ),
        ));
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->removeDirectory(sys_get_temp_dir().'/composer-test');
        $fs->removeDirectory(sys_get_temp_dir().'/composer-test-cache');
    }

    public function getAssetTypes()
    {
        return array(
            array('npm', 'package.json'),
            array('bower', 'bower.json'),
        );
    }

    /**
     * @dataProvider getAssetTypes
     *
     * @param string $type
     * @param string $filename
     */
    public function testPublicRepositoryWithComposer($type, $filename)
    {
        $repoBaseUrl = 'https://bitbucket.org/composer-test/repo-name';
        $repoUrl = $repoBaseUrl.'.git';
        $repoApiUrl = 'https://api.bitbucket.org/1.0/repositories/composer-test/repo-name';
        $identifier = 'v0.0.0';
        $sha = 'SOMESHA';

        $io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
        $io->expects($this->any())
            ->method('isInteractive')
            ->will($this->returnValue(true));

        $remoteFilesystem = $this->getMockBuilder('Composer\Util\RemoteFilesystem')
            ->setConstructorArgs(array($io))
            ->getMock();

        $remoteFilesystem->expects($this->at(0))
            ->method('getContents')
            ->with($this->equalTo('bitbucket.org'), $this->equalTo($this->getScheme($repoApiUrl)), $this->equalTo(false))
            ->will($this->returnValue($this->createJsonComposer(array('main_branch' => 'test_master'))));

        $remoteFilesystem->expects($this->at(1))
            ->method('getContents')
            ->with(
                $this->equalTo('bitbucket.org'),
                $this->equalTo($repoApiUrl.'/src/'.$identifier.'/'.$filename),
                $this->equalTo(false)
            )
            ->will($this->returnValue($this->createApiJsonWithRepoData(array())));

        $repoConfig = array(
            'url' => $repoUrl,
            'asset-type' => $type,
            'filename' => $filename,
        );

        /* @var IOInterface $io */
        /* @var RemoteFilesystem $remoteFilesystem */

        $driver = new GitBitbucketDriver($repoConfig, $io, $this->config, null, $remoteFilesystem);
        $driver->initialize();
        $this->setAttribute($driver, 'tags', array($identifier => $sha));

        $this->assertEquals('test_master', $driver->getRootIdentifier());

        $dist = $driver->getDist($sha);
        $this->assertEquals('zip', $dist['type']);
        $this->assertEquals($this->getScheme($repoBaseUrl).'/get/SOMESHA.zip', $dist['url']);
        $this->assertEquals($sha, $dist['reference']);

        $source = $driver->getSource($sha);
        $this->assertEquals('git', $source['type']);
        $this->assertEquals($repoUrl, $source['url']);
        $this->assertEquals($sha, $source['reference']);

        $driver->getComposerInformation($identifier);
    }

    /**
     * @dataProvider getAssetTypes
     *
     * @param string $type
     * @param string $filename
     */
    public function testPublicRepositoryWithEmptyComposer($type, $filename)
    {
        $repoBaseUrl = 'https://bitbucket.org/composer-test/repo-name';
        $repoUrl = $repoBaseUrl.'.git';
        $repoApiUrl = 'https://api.bitbucket.org/1.0/repositories/composer-test/repo-name';
        $identifier = 'v0.0.0';
        $io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();

        $remoteFilesystem = $this->getMockBuilder('Composer\Util\RemoteFilesystem')
            ->setConstructorArgs(array($io))
            ->getMock();

        $remoteFilesystem->expects($this->at(0))
            ->method('getContents')
            ->with(
                $this->equalTo('bitbucket.org'),
                $this->equalTo($repoApiUrl.'/src/'.$identifier.'/'.$filename),
                $this->equalTo(false)
            )
            ->will($this->throwException(new TransportException('Not Found', 404)));

        $repoConfig = array(
            'url' => $repoUrl,
            'asset-type' => $type,
            'filename' => $filename,
        );

        /* @var IOInterface $io */
        /* @var RemoteFilesystem $remoteFilesystem */

        $driver = new GitBitbucketDriver($repoConfig, $io, $this->config, null, $remoteFilesystem);
        $driver->initialize();

        $validEmpty = array(
            '_nonexistent_package' => true,
        );

        $this->assertSame($validEmpty, $driver->getComposerInformation($identifier));
    }

    /**
     * @param object $object
     * @param string $attribute
     * @param mixed  $value
     */
    protected function setAttribute($object, $attribute, $value)
    {
        $attr = new \ReflectionProperty($object, $attribute);
        $attr->setAccessible(true);
        $attr->setValue($object, $value);
    }

    /**
     * Creates the json composer content.
     *
     * @param array  $content The composer content
     * @param string $name    The name of repository
     *
     * @return string The json content
     */
    protected function createJsonComposer(array $content, $name = 'repo-name')
    {
        return json_encode(array_merge_recursive($content, array(
            'name' => $name,
        )));
    }

    /**
     * @param array  $content The composer content
     * @param string $name    The name of repository
     *
     * @return string The API return value with the json content
     */
    protected function createApiJsonWithRepoData(array $content, $name = 'repo-name')
    {
        $composerContent = $this->createJsonComposer($content, $name);

        return json_encode(
            array(
                'node' => 'nodename',
                'path' => '/path/to/file',
                'data' => $composerContent,
                'size' => strlen($composerContent),
            )
        );
    }

    /**
     * Get the url with https or http protocol depending on SSL support.
     *
     * @param string $url
     *
     * @return string The correct url
     */
    protected function getScheme($url)
    {
        if (extension_loaded('openssl')) {
            return $url;
        }

        return str_replace('https:', 'http:', $url);
    }
}
