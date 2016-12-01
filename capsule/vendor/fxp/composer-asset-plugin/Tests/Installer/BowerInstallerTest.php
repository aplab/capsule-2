<?php

/*
 * This file is part of the Fxp Composer Asset Plugin package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Composer\AssetPlugin\Tests\Installer;

use Composer\Downloader\DownloadManager;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Package\Package;
use Composer\Package\RootPackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Composer\TestCase;
use Composer\Composer;
use Composer\Config;
use Fxp\Composer\AssetPlugin\Installer\BowerInstaller;
use Fxp\Composer\AssetPlugin\Type\AssetTypeInterface;
use Fxp\Composer\AssetPlugin\Util\AssetPlugin;

/**
 * Tests of bower asset installer.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class BowerInstallerTest extends TestCase
{
    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $vendorDir;

    /**
     * @var string
     */
    protected $binDir;

    /**
     * @var DownloadManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dm;

    /**
     * @var InstalledRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var IOInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $io;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var AssetTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $type;

    protected function setUp()
    {
        $this->fs = new Filesystem();

        $this->composer = new Composer();
        $this->config = new Config();
        $this->composer->setConfig($this->config);

        $this->vendorDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'composer-test-vendor';
        $this->ensureDirectoryExistsAndClear($this->vendorDir);

        $this->binDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'composer-test-bin';
        $this->ensureDirectoryExistsAndClear($this->binDir);

        $this->config->merge(array(
                'config' => array(
                    'vendor-dir' => $this->vendorDir,
                    'bin-dir' => $this->binDir,
                ),
            ));

        $this->dm = $this->getMockBuilder('Composer\Downloader\DownloadManager')
            ->disableOriginalConstructor()
            ->getMock();
        /* @var DownloadManager $dm */
        $dm = $this->dm;
        $this->composer->setDownloadManager($dm);

        $this->repository = $this->getMockBuilder('Composer\Repository\InstalledRepositoryInterface')->getMock();
        $this->io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();

        $this->type = $this->getMockBuilder('Fxp\Composer\AssetPlugin\Type\AssetTypeInterface')->getMock();
        $this->type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->type->expects($this->any())
            ->method('getComposerVendorName')
            ->will($this->returnValue('foo-asset'));
        $this->type->expects($this->any())
            ->method('getComposerType')
            ->will($this->returnValue('foo-asset-library'));
        $this->type->expects($this->any())
            ->method('getFilename')
            ->will($this->returnValue('foo.json'));
        $this->type->expects($this->any())
            ->method('getVersionConverter')
            ->will($this->returnValue($this->getMockBuilder('Fxp\Composer\AssetPlugin\Converter\VersionConverterInterface')->getMock()));
        $this->type->expects($this->any())
            ->method('getPackageConverter')
            ->will($this->returnValue($this->getMockBuilder('Fxp\Composer\AssetPlugin\Converter\PackageConverterInterface')->getMock()));
    }

    protected function tearDown()
    {
        $this->fs->removeDirectory($this->vendorDir);
        $this->fs->removeDirectory($this->binDir);
    }

    public function testInstallerCreationShouldNotCreateVendorDirectory()
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->fs->removeDirectory($this->vendorDir);
        $this->composer->setPackage($rootPackage);

        new BowerInstaller($io, $this->composer, $type);
        $this->assertFileNotExists($this->vendorDir);
    }

    public function testInstallerCreationShouldNotCreateBinDirectory()
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->fs->removeDirectory($this->binDir);
        $this->composer->setPackage($rootPackage);

        new BowerInstaller($io, $this->composer, $type);
        $this->assertFileNotExists($this->binDir);
    }

    public function testIsInstalled()
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->composer->setPackage($rootPackage);

        $library = new BowerInstaller($io, $this->composer, $type);
        /* @var \PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this->createPackageMock();
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('foo-asset/package'));

        /* @var PackageInterface $package */
        $packageDir = $this->vendorDir.'/'.$package->getPrettyName();
        mkdir($packageDir, 0777, true);

        /* @var \PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->repository;
        $repository
            ->expects($this->exactly(2))
            ->method('hasPackage')
            ->with($package)
            ->will($this->onConsecutiveCalls(true, false));

        /* @var InstalledRepositoryInterface $repository */
        $this->assertTrue($library->isInstalled($repository, $package));
        $this->assertFalse($library->isInstalled($repository, $package));

        $this->ensureDirectoryExistsAndClear($packageDir);
    }

    public function getAssetIgnoreFiles()
    {
        return array(
            array(array()),
            array(array('foo', 'bar')),
        );
    }

    public function getAssetMainFiles()
    {
        return array(
            array(array()),
            array(array(
                'asset-main-files' => array(
                    'foo-asset/bar' => array(
                        'foo',
                        'bar',
                    ),
                ),
            )),
        );
    }

    /**
     * @dataProvider getAssetIgnoreFiles
     *
     * @param array $ignoreFiles
     */
    public function testInstall(array $ignoreFiles)
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->composer->setPackage($rootPackage);

        $library = new BowerInstaller($io, $this->composer, $type);
        /* @var \PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this->createPackageMock($ignoreFiles);
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('foo-asset/package'));

        /* @var PackageInterface $package */
        $packageDir = $this->vendorDir.'/'.$package->getPrettyName();
        mkdir($packageDir, 0777, true);

        /* @var \PHPUnit_Framework_MockObject_MockObject $dm */
        $dm = $this->dm;
        $dm
            ->expects($this->once())
            ->method('download')
            ->with($package, $this->vendorDir.DIRECTORY_SEPARATOR.'foo-asset/package');

        /* @var \PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->repository;
        $repository
            ->expects($this->once())
            ->method('addPackage')
            ->with($package);

        /* @var InstalledRepositoryInterface $repository */
        $library->install($repository, $package);
        $this->assertFileExists($this->vendorDir, 'Vendor dir should be created');
        $this->assertFileExists($this->binDir, 'Bin dir should be created');

        $this->ensureDirectoryExistsAndClear($packageDir);
    }

    /**
     * @dataProvider getAssetIgnoreFiles
     *
     * @param array $ignoreFiles
     */
    public function testUpdate(array $ignoreFiles)
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->composer->setPackage($rootPackage);

        $library = new BowerInstaller($io, $this->composer, $type);
        /* @var \PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this->createPackageMock($ignoreFiles);
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('foo-asset/package'));

        /* @var PackageInterface $package */
        $packageDir = $this->vendorDir.'/'.$package->getPrettyName();
        mkdir($packageDir, 0777, true);

        /* @var \PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->repository;

        $repository
            ->expects($this->exactly(2))
            ->method('hasPackage')
            ->with($package)
            ->will($this->returnValue(true));

        /* @var InstalledRepositoryInterface $repository */
        $library->update($repository, $package, $package);
        $this->assertFileExists($this->vendorDir, 'Vendor dir should be created');
        $this->assertFileExists($this->binDir, 'Bin dir should be created');

        $this->ensureDirectoryExistsAndClear($packageDir);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUninstall()
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->composer->setPackage($rootPackage);

        $library = new BowerInstaller($io, $this->composer, $type);
        $package = $this->createPackageMock();

        /* @var \PHPUnit_Framework_MockObject_MockObject $package */
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('foo-asset/pkg'));

        /* @var \PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->repository;
        $repository
            ->expects($this->exactly(2))
            ->method('hasPackage')
            ->with($package)
            ->will($this->onConsecutiveCalls(true, false));

        $repository
            ->expects($this->once())
            ->method('removePackage')
            ->with($package);

        /* @var \PHPUnit_Framework_MockObject_MockObject $dm */
        $dm = $this->dm;
        $dm
            ->expects($this->once())
            ->method('remove')
            ->with($package, $this->vendorDir.DIRECTORY_SEPARATOR.'foo-asset/pkg');

        /* @var InstalledRepositoryInterface $repository */
        /* @var PackageInterface $package */
        $library->uninstall($repository, $package);

        $library->uninstall($repository, $package);
    }

    public function testGetInstallPath()
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->composer->setPackage($rootPackage);

        $library = new BowerInstaller($io, $this->composer, $type);
        $package = $this->createPackageMock();

        /* @var \PHPUnit_Framework_MockObject_MockObject $package */
        $package
            ->expects($this->once())
            ->method('getTargetDir')
            ->will($this->returnValue(null));
        $package
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo-asset/bar'));
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('foo-asset/bar'));

        /* @var PackageInterface $package */
        $exceptDir = $this->vendorDir.'/'.$package->getName();
        $exceptDir = str_replace('\\', '/', $exceptDir);
        $packageDir = $library->getInstallPath($package);
        $packageDir = str_replace('\\', '/', $packageDir);

        $this->assertEquals($exceptDir, $packageDir);
    }

    public function testGetInstallPathWithTargetDir()
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock();
        /* @var IOInterface $io */
        $io = $this->io;
        /* @var AssetTypeInterface $type */
        $type = $this->type;

        $this->composer->setPackage($rootPackage);

        $library = new BowerInstaller($io, $this->composer, $type);
        $package = $this->createPackageMock();

        /* @var \PHPUnit_Framework_MockObject_MockObject $package */
        $package
            ->expects($this->once())
            ->method('getTargetDir')
            ->will($this->returnValue('Some/Namespace'));
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('foo-asset/bar'));

        /* @var PackageInterface $package */
        $exceptDir = $this->vendorDir.'/'.$package->getPrettyName().'/Some/Namespace';
        $exceptDir = str_replace('\\', '/', $exceptDir);
        $packageDir = $library->getInstallPath($package);
        $packageDir = str_replace('\\', '/', $packageDir);

        $this->assertEquals($exceptDir, $packageDir);
    }

    /**
     * @dataProvider getAssetMainFiles
     *
     * @param array $mainFiles
     */
    public function testMainFiles(array $mainFiles)
    {
        /* @var RootPackageInterface $rootPackage */
        $rootPackage = $this->createRootPackageMock($mainFiles);
        $this->composer->setPackage($rootPackage);

        $package = new Package('foo-asset/bar', '1.0.0', '1.0.0');
        $package = AssetPlugin::addMainFiles($this->composer, $package);
        $extra = $package->getExtra();
        if (isset($mainFiles['asset-main-files'])) {
            $this->assertEquals($extra['bower-asset-main'], $mainFiles['asset-main-files']['foo-asset/bar']);
        } else {
            $this->assertEquals($extra, array());
        }
    }

    /**
     * @param array $ignoreFiles
     *
     * @return PackageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createPackageMock(array $ignoreFiles = array())
    {
        $package = $this->getMockBuilder('Composer\Package\Package')
            ->setConstructorArgs(array(md5(mt_rand()), '1.0.0.0', '1.0.0'))
            ->getMock();

        $package
            ->expects($this->any())
            ->method('getExtra')
            ->will($this->returnValue(array(
                'bower-asset-ignore' => $ignoreFiles,
            )));

        return $package;
    }

    /**
     * @param array $mainFiles
     *
     * @return RootPackageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRootPackageMock(array $mainFiles = array())
    {
        $package = $this->getMockBuilder('Composer\Package\RootPackageInterface')
            ->setConstructorArgs(array(md5(mt_rand()), '1.0.0.0', '1.0.0'))
            ->getMock();

        $package
            ->expects($this->any())
            ->method('getExtra')
            ->will($this->returnValue($mainFiles));

        return $package;
    }
}
