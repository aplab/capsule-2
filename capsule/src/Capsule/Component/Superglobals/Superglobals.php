<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 22.11.2016
 * Time: 8:40
 */

namespace Capsule\Component\HttpRequest;
use Capsule\Component\Superglobals\Cookie;
use Capsule\Component\Superglobals\Env;
use Capsule\Component\Superglobals\Exception;
use Capsule\Component\Superglobals\Files;
use Capsule\Component\Superglobals\Get;
use Capsule\Component\Superglobals\Post;
use Capsule\Component\Superglobals\Request;
use Capsule\Component\Superglobals\Server;
use Capsule\Component\Superglobals\Session;


/**
 * Class HttpRequest
 * @package Capsule\Component\HttpRequest
 * @property Server $server
 * @property Get $get
 * @property Post $post
 * @property Files $files
 * @property Cookie $cookie
 * @property Session $session
 * @property Request $request
 * @property Env $env
 */
class Superglobals
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $name
     * @return DataSet
     * @throws Exception
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            switch ($name) {
                case 'server' :
                    $this->data[$name] = new Server;
                    break;
                case 'get' :
                    $this->data[$name] = new Get;
                    break;
                case 'post' :
                    $this->data[$name] = new Post;
                    break;
                case 'files' :
                    $this->data[$name] = new Files;
                    break;
                case 'cookie' :
                    $this->data[$name] = new Cookie;
                    break;
                case 'session' :
                    $this->data[$name] = new Session;
                    break;
                case 'request' :
                    $this->data[$name] = new Request;
                    break;
                case 'env' :
                    $this->data[$name] = new Env;
                    break;
                default:
                    throw new Exception('unknown property: ' . $name);
            }
        }
        return $this->data[$name];
    }

    /**
     * @throws Exception
     */
    public function __set()
    {
        throw new Exception('Modification not allowed');
    }
}