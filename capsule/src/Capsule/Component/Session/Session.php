<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 01.11.2016
 * Time: 1:11
 */

namespace Capsule\Component\Session;


use Capsule\Core\Singleton;

class Session extends Singleton
{
    protected function __construct()
    {
        $lifetime = 86400;
        session_cache_expire(720);
        session_start([
            'name' => 'CS2SESSID',
            'cookie_httponly' => true,
            'use_trans_sid' => false,
            'use_only_cookies' => true,
            'use_cookies' => true,
            'cookie_lifetime' => $lifetime,
            'gc_maxlifetime' => $lifetime,
            'use_strict_mode' => true
        ]);
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            session_id(),
            time() + $lifetime,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        $key = $this->k($name);
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $key = $this->k($name);
        $_SESSION[$key] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->exists($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        $key = $this->k($name);
        return array_key_exists($key, $_SESSION);
    }

    /**
     * @param $name
     * @return string
     */
    private function k($name)
    {
        return md5(serialize($name));
    }

    /**
     * Уничтожить переменную
     *
     * @param string $name
     */
    public function purge($name)
    {
        $key = $this->k($name);
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }
}