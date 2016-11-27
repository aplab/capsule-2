<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 02.11.2016
 * Time: 23:21
 */

namespace Capsule\User;


use Capsule\Component\Session\Session;
use Capsule\Component\Session\SessionData;
use Capsule\Core\Singleton;

/**
 * Class Auth
 * @package Capsule\User
 */
class Auth extends Singleton
{
    /**
     *
     */
    const POST_VAR_USERNAME = 'login_as';
    /**
     *
     */
    const POST_VAR_PASSWORD = 'password';
    /**
     *
     */
    const SESSION_KEY_USER_ID = 'user_id';
    /**
     *
     */
    const SESSION_KEY_SERVER_VARS = 'server';
    /**
     *
     */
    const AUTH_METHOD_FORM = 'form';
    /**
     *
     */
    const AUTH_METHOD_SESSION = 'session';

    /**
     * @var array
     */
    protected $serverCheckKeys = [
        'HTTP_ACCEPT_ENCODING' => false,
        'HTTP_ACCEPT_LANGUAGE' => true,
        'HTTP_ACCEPT' => false,
        'HTTP_USER_AGENT' => true,
        'SERVER_NAME' => true,
        'SERVER_PORT' => true,
        'SERVER_ADDR' => true,
        'REMOTE_ADDR' => true
    ];

    /**
     * @var SessionData
     */
    protected $session;

    /**
     * @var User
     */
    protected $user;

    /**
     * Как был авторизован пользователь, через форму или через сессию
     *
     * @var string
     */
    protected $method;

    /**
     * @return mixed
     */
    public function method()
    {
        return $this->method();
    }

    /**
     * Auth constructor.
     */
    protected function __construct()
    {
        $this->session = Session::getInstance()->get(static::class);
        $this->auth();
        if ($this->user instanceof User) {
            return;
        }
        $this->login();

    }

    /**
     * Cookies auth
     */
    protected function auth()
    {
        $id = $this->session->get(static::SESSION_KEY_USER_ID, null);
        if (!preg_match('/^\\d+$/', $id)) {
            return;
        }
        $user = User::id($id);
        if (!($user instanceof User)) {
            return;
        }
        $server = $this->session->get(static::SESSION_KEY_SERVER_VARS, []);
        foreach ($this->serverCheckKeys as $key => $check) {
            if (!$check) {
                continue;
            }
            if (!array_key_exists($key, $_SERVER)) {
                return;
            }
            if (!array_key_exists($key, $server)) {
                return;
            }
            if ($_SERVER[$key] !== $server[$key]) {
                return;
            }
        }
        $this->user = $user;
        $this->method = static::AUTH_METHOD_SESSION;
    }

    /**
     * Form vars login
     */
    protected function login()
    {
        if (!isset($_POST[static::POST_VAR_USERNAME])) {
            return;
        }
        if (!isset($_POST[static::POST_VAR_PASSWORD])) {
            return;
        }
        $username = $_POST[static::POST_VAR_USERNAME];
        $password = $_POST[static::POST_VAR_PASSWORD];
        if (!strlen($username)) {
            return;
        }
        if (!strlen($password)) {
            return;
        }
        $user = User::getElementByLogin($username);
        if (!($user instanceof User)) {
            return;
        }
        if (!$user->password($password)) {
            return;
        }
        $this->user = $user;
        $this->session->{static::SESSION_KEY_USER_ID} = $user->id;
        $server = [];
        foreach ($this->serverCheckKeys as $key => $check) {
            if (!$check) {
                continue;
            }
            $server[$key] = null;
            if (array_key_exists($key, $_SERVER)) {
                $server[$key] = $_SERVER[$key];
            }
        }
        $this->session->{static::SESSION_KEY_SERVER_VARS} = $server;
        $this->method = static::AUTH_METHOD_FORM;
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return mixed|null
     */
    public static function userId()
    {
        $u = static::getInstance()->user();
        return $u ? $u->id : null;
    }

    /**
     * logout
     */
    public function logout()
    {
        $this->user = null;
        $this->session->{static::SESSION_KEY_USER_ID} = null;
        $this->session->{static::SESSION_KEY_SERVER_VARS} = [];
        session_destroy();
    }
}