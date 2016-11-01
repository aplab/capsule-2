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
use Capsule\Exception;

/**
 * Class Session
 * @package Capsule\Component\Session
 */
class Session extends Singleton
{
    /**
     * Session constructor.
     */
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
     * @return mixed
     */
    public function get($name)
    {
        $key = self::k($name);
        if (!array_key_exists($key, $_SESSION)) {
            $_SESSION[$key] = new SessionData;
        }
        if (!($_SESSION[$key] instanceof SessionData)) {
            $_SESSION[$key] = new SessionData;
        }
        return $_SESSION[$key];
    }

    /**
     * @param $name
     * @param $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception('direct modification not allowed');
    }

    /**
     * @param $name
     * @return string
     */
    private static function k($name)
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