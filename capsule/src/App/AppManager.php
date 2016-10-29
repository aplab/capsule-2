<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 23.10.2016
 * Time: 20:50
 */

namespace App;


use Capsule\Capsule;
use Capsule\Core\Singleton;

class AppManager extends Singleton
{
    /**
     * Application classname
     *
     * @var string
     */
    protected $appClass;

    /**
     * Application id
     *
     * @var string
     */
    protected $id;

    /**
     * Configuration data
     *
     * @var array
     */
    protected $config;

    /**
     * AppManager constructor.
     */
    protected function __construct()
    {
        $this->config = Capsule::getInstance()->config->app->toArray();
    }

    /**
     * @throws \Exception
     */
    public function selectApp()
    {
        $tmp = array();
        foreach ($this->config as $id => $app) {
            $id = trim($id, '/');
            $items = array_filter(explode('/', $id));
            $tmp[$id] = [
                'app' => $app,
                'items' => $items
            ];
        }
        uksort($tmp, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });
        $request_uri = getenv('REQUEST_URI');
        $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
        if (false === $path) {
            $msg = 'Seriously malformed URL';
            throw new \Exception($msg);
        }
        $path = array_filter(explode('/', $path));
        foreach ($tmp as $id => $data) {
            $items = $data['items'];
            $length = sizeof($items);
            $slice = array_slice($path, 0, $length);
            if ($slice === $items) {
                $this->id = $id;
                $this->appClass = $data['app'];
                break;
            }
        }
        $app = $this->appClass;
        return $app::getInstance();
    }
}