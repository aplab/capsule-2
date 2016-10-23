<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 31.01.2014 0:20:31 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\User;

use Capsule\Core\Singleton;
use Capsule\Filter\Inflector;
use Capsule\Db\Db;
use Capsule\Exception;
use Capsule\Superglobals\Server;
use Capsule\Superglobals\Post;
use Capsule\Util\Keygen;
use Capsule\Superglobals\Cookie;
/**
 * Auth.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 * @property User $currentUser
 */
class Auth extends Singleton
{
    /**
     * Залогинившийся пользователь
     *
     * @var User
     */
    protected $currentUser;
    
    /**
     *
     * @param void
     * @return \Capsule\User\User
     */
    protected function getCurrentUser() {
        return $this->currentUser;
    }
    
    /**
     * shared data
     *
     * @var unknown
     */
    protected static $common = array();
    
    /**
     * key name logout
     *
     * @var string
     */
    const LOGOUT = 'logout';
    
    /**
     * logout
     *
     * @param void
     * @return void
     */
    public static function logout() {
        self::$common[get_called_class()][self::LOGOUT] = true;
    }
    
    /**
     * Максимальное количество символов http-заголовков для сравнения.
     * Например, если длина значения HTTP_USER_AGENT превысит
     *
     * Влияет на некоторые создаваемые поля в таблице.
     *
     * @var int
     */
    protected $headersMaxlength = 1024;

    /**
     * Неопределенное значение
     *
     * @var string
     */
    protected $undefined = 'UNDEFINED';

    /**
     * Содержит наименование связанной таблицы
     *
     * @var string
     */
    protected $table;

    /**
     * Интервал времени после которого истекает срок действия cookie (в секундах)
     *
     * @var int
     */
    protected $sessionLifetime = 2592000;//60*60*24*30

    /**
     * Служебное поле time() + $this->sessionLifetime
     *
     * @var int
     */
    protected $sessionExpireTime;

    /**
     * Имя ключа элемента массива $_POST, содержащего имя входа.
     *
     * @var string
     */
    protected $keyNameLogin = 'login_as';

    /**
     * Возвращает имя переменной для передачи логина из формы
     *
     * @param void
     * @return string
     */
    protected function getKeyNameLogin() {
        return $this->keyNameLogin;
    }

    /**
     * Имя ключа элемента массива $_POST, содержащего пароль.
     *
     * @var string
     */
    protected $keyNamePassword = 'password';

    /**
     * Возвращает имя переменной для передачи пароля из формы
     *
     * @param void
     * @return string
     */
    protected function getKeyNamePassword() {
        return $this->keyNamePassword;
    }

    /**
     * После каждого действия менять значение cookie
     * Параноидальный режим, возможны частые вылеты.
     * Наверное он не нужен.
     *
     * @var boolean
     */
    protected $alwaysOverrideSessionId = false;#true;

    /**
     * Разрешить редирект при входе в систему через форму.
     * Введено из за странного поведения IE6 при авторизации.
     * Возможно, в дальнейшем это не понадобится.
     *
     * @var bool
     */
    protected $allowRedirect = true;

    /**
     * Игнорировать проверку HTTP_ACCEPT для MS Internet Explorer
     *
     * @var boolean
     */
    protected $ignoreMsieHttpAccept = true;

    /**
     * Проверять совпадение IP - адреса
     *
     * @var boolean
     */
    protected $checkIpAddress = true;

    /**
     * Проверять совпадение HTTP_USER_AGENT
     *
     * @var boolean
     */
    protected $checkHttpUserAgent = true;

    /**
     * Проверять совпадение HTTP_ACCEPT
     *
     * @var boolean
     */
    protected $checkHttpAccept = false;

    /**
     * Проверять совпадение HTTP_ACCEPT_ENCODING
     *
     * @var boolean
     */
    protected $checkHttpAcceptEncoding = false;

    /**
     * Проверять совпадение HTTP_ACCEPT_LANGUAGE
     *
     * @var boolean
     */
    protected $checkHttpAcceptLanguage = true;

    /**
     * Переменные окружения
     *
     * @var string
     */
    protected $ipAddress;
    protected $httpUserAgent;
    protected $httpAccept;
    protected $httpAcceptEncoding;
    protected $httpAcceptLanguage;

    /**
     * Хэши переменных окружения
     *
     * @var string
     */
    protected $hashIpAddress;
    protected $hashHttpUserAgent;
    protected $hashHttpAccept;
    protected $hashHttpAcceptEncoding;
    protected $hashHttpAcceptLanguage;

    /**
     * Имя переменной, содержащей идентификатор сессии. (если не задано,
     * то формируется автоматически)
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Значение ключа из cookie
     *
     * @var string
     */
    protected $sessionId;

    /**
     * internet explorer
     *
     * @var boolean
     */
    protected $isMsie;

    /**
     * Защищенный конструктор
     *
     * Maximum length of the textual representation of an IPv6 address
     * 8 * 4 + 7 = 39
     * 8 groups of 4 digits with 7 ':' between them.
     * Or, if you want to take into account the IPv4 tunneling features
     * [0000:0000:0000:0000:0000:0000:192.168.0.1],
     * (6 * 4 + 5) + 1 + (4 * 3 + 3) = 29 + 1 + 15 = 45
     *
     * @param void
     * @return self
     * @throws Exception
     */
    protected function __construct() {#die(__LINE__);
        $this->table = Inflector::getInstance()->getAssociatedTable($this);
        $db = Db::getInstance();
        if (!$db->tableExists($this->table)) {
            $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
                    `user_id` SMALLINT UNSIGNED NOT NULL COMMENT "идентификатор пользователя",
                    `session_id` VARBINARY(200) DEFAULT NULL COMMENT "значение COOKIE",
                    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "активная сессия",

                    `ip_address` VARCHAR(46) DEFAULT NULL COMMENT "ip-адрес",
                    `http_user_agent` VARCHAR(' . $this->headersMaxlength . ') DEFAULT NULL COMMENT "HTTP_USER_AGENT",
                    `http_accept` VARCHAR(' . $this->headersMaxlength . ') DEFAULT NULL COMMENT "HTTP_ACCEPT",
                    `http_accept_language` VARCHAR(' . $this->headersMaxlength . ') DEFAULT NULL COMMENT "HTTP_ACCCEPT_LANGUAGE",
                    `http_accept_encoding` VARCHAR(' . $this->headersMaxlength . ') DEFAULT NULL COMMENT "HTTP_ACCEPT_ENCODING",

                    `hash_ip_address` VARBINARY(32) DEFAULT NULL COMMENT "хэш от ip-адреса",
                    `hash_http_user_agent` VARBINARY(32) DEFAULT NULL COMMENT "хэш от HTTP_USER_AGENT",
                    `hash_http_accept` VARBINARY(32) DEFAULT NULL COMMENT "хэш от HTTP_ACCEPT",
                    `hash_http_accept_language` VARBINARY(32) DEFAULT NULL COMMENT "хэш от HTTP_ACCCEPT_LANGUAGE",
                    `hash_http_accept_encoding` VARBINARY(32) DEFAULT NULL COMMENT "хэш от HTTP_ACCEPT_ENCODING",

                    `created` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00" COMMENT "дата создания",
                    `last_logged_in` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00" COMMENT "дата последнего посещения",

                    PRIMARY KEY (`session_id`),
                    KEY `user_id` (`user_id`),
                    KEY `last_logged_in` (`last_logged_in`),
                    KEY `active` (`active`, `last_logged_in`))
                    ENGINE = InnoDB COMMENT = "auth"';
            $db->query($sql);
            if (!$db->tableExists($this->table, true)) {
                $msg = 'Unable to create table ' . $this->table;
                throw new Exception($msg);
            }
        }
        $server = Server::getInstance();
        $this->ipAddress = substr($server->REMOTE_ADDR, 0, 46);
        $this->hashIpAddress = md5($this->ipAddress);

        $this->httpUserAgent = substr($server->get('HTTP_USER_AGENT',
                $this->undefined), 0, $this->headersMaxlength);
        $this->hashHttpUserAgent = md5($this->httpUserAgent);

        $this->httpAccept = substr($server->get('HTTP_ACCEPT',
                $this->undefined), 0, $this->headersMaxlength);
        $this->hashHttpAccept = md5($this->httpAccept);

        $this->httpAcceptEncoding = substr($server->get('HTTP_ACCEPT_ENCODING',
                $this->undefined), 0, $this->headersMaxlength);
        $this->hashHttpAcceptEncoding = md5($this->httpAcceptEncoding);

        $this->httpAcceptLanguage = substr($server->get('HTTP_ACCEPT_LANGUAGE',
                $this->undefined), 0, $this->headersMaxlength);
        $this->hashHttpAcceptLanguage = md5($this->httpAcceptLanguage);
        // trying to detect msie
        if (false !== strpos(strtolower($this->httpUserAgent), 'msie')) {
            $this->isMsie = true;
        }
        $this->sessionExpireTime = time() + $this->sessionLifetime;
        $this->sessionName = Inflector::getInstance()->getClassKey($this);
        $this->login();
    }

    /**
     * Идентифицировать пользователя
     *
     * @param void
     * @return void
     */
    protected function login() {
        if ($this->cookieLogin()) {
            return;
        }
        if ($this->formLogin()) {
            return;
        }
    }

    /**
     * Идентифицировать пользователя используя cookie
     *
     * @param
     *            void
     * @return void
     */
    protected function cookieLogin() {
        $cookie = Cookie::getInstance();
        $this->sessionId = $cookie->get($this->sessionName, null);
        if (!$this->sessionId) {
            return false;
        }
        $data = $this->loadSessionData();
        if (! is_array($data)) {
            return false;
        }
        
        if ($this->checkIpAddress) {
            if ($this->hashIpAddress !== $data['hash_ip_address']) {
                return false;
            }
        }
        
        if ($this->checkHttpUserAgent) {
            if ($this->hashHttpUserAgent !== $data['hash_http_user_agent']) {
                return false;
            }
        }
        
        if ($this->checkHttpAccept) {
            if ($this->hashHttpAccept !== $data['hash_http_accept']) {
                if (! ($this->isMsie && $this->ignoreMsieHttpAccept)) {
                    return false;
                }
                return false;
            }
        }
        
        if ($this->checkHttpAcceptLanguage) {
            if ($this->hashHttpAcceptLanguage !== $data['hash_http_accept_language']) {
                return false;
            }
        }
        
        if ($this->checkHttpAcceptEncoding) {
            if ($this->hashHttpAcceptEncoding !== $data['hash_http_accept_encoding']) {
                return false;
            }
        }
        
        $user = User::getElementById($data['user_id']);
        if (! $user) {
            return false;
        }
//         if (! $user->active) {
//             return false;
//         }
        
        $this->currentUser = $user;
        if ($this->destroySession()) { // в случае logout не обновляем сессию и не ставим cookie
            return true; // возврат true чтобы выйти из вызывающей функции без вызова авторизации через форму
        }
        $this->updateSession();
        $set = $this->setCookie();
        return true;
    }

    /**
     * Выход
     *
     * @param void
     * @return void
     */
    protected function destroySession() {
        if (!isset(self::$common[get_class($this)][self::LOGOUT])) {
            return false;
        }
        $db = Db::getInstance();
        if ($this->currentUser instanceof User) {
            $sql = 'DELETE FROM `' . $this->table . '`
                    WHERE `session_id` = ' . $db->qt($this->sessionId);
            $result = $db->query($sql);
            $this->currentUser = null;
            $this->deleteCookie();
        }
        return true;
    }

    /**
     * Generate new key
     *
     * @param void
     * @return string
     */
    protected function newKey() {
        return Keygen::getInstance()->generate(180, 10);
    }
    
    /**
     * Обновить данные сессии
     *
     * @param
     *            void
     * @return boolean
     */
    protected function updateSession() {
        if (! ($this->currentUser instanceof User)) {
            $msg = 'Trying to save session without current user';
            throw new Exception($msg);
        }
        $session_id = $this->sessionId;
        if ($this->alwaysOverrideSessionId) {
            $this->sessionId = $this->newKey();
        }
        $db = Db::getInstance();
        $sql = 'UPDATE `' . $this->table . '` SET
                `user_id`                   = ' . $db->qt($this->currentUser->id) . ',
                `session_id`                = ' . $db->qt($this->sessionId) . ',
                `active`                    = 1,

                `ip_address`                = ' . $db->qt($this->ipAddress) . ',
                `http_user_agent`           = ' . $db->qt($this->httpUserAgent) . ',
                `http_accept`               = ' . $db->qt($this->httpAccept) . ',
                `http_accept_language`      = ' . $db->qt($this->httpAcceptLanguage) . ',
                `http_accept_encoding`       = ' . $db->qt($this->httpAcceptEncoding) . ',

                `hash_ip_address`           = ' . $db->qt($this->hashIpAddress) . ',
                `hash_http_user_agent`      = ' . $db->qt($this->hashHttpUserAgent) . ',
                `hash_http_accept`          = ' . $db->qt($this->hashHttpAccept) . ',
                `hash_http_accept_language` = ' . $db->qt($this->hashHttpAcceptLanguage) . ',
                `hash_http_accept_encoding`  = ' . $db->qt($this->hashHttpAcceptEncoding) . ',

                `last_logged_in`            = NOW()
                WHERE `session_id` = ' . $db->qt($session_id);
        $result = $db->query($sql);
        return (boolean) $db->affected_rows;
    }
    
    /**
     * Авторизация через форму
     *
     * @param void
     * @return boolean
     */
    protected function formLogin() {
        $post = Post::getInstance();
        $server = Server::getInstance();
        $login = $post->get($this->keyNameLogin, null);
        $password = $post->get($this->keyNamePassword, null);
        if (!($login && $password)) {
            return false;
        }
        $user = User::getElementByLogin($login);
        if (!$user) {
            return false;
        }
        if (!$user->password($password)) {
            return false;
        }
        $this->currentUser = $user;
        $this->sessionId = $this->newKey();
        $this->setCookie();
        $this->createSession();
        if ($this->allowRedirect) {
            $this->redirect();
        }
        return true;
    }
    
    /**
     * setcookie wrapper
     *
     * @param void
     * @return void
     */
    protected function setCookie() {
        $this->_setCookie($this->sessionId, $this->sessionExpireTime);
    }
    
    /**
     * setcookie wrapper
     *
     * @param void
     * @return void
     */
    protected function deleteCookie() {
        $this->_setCookie('', time() - 3600);
        $this->sessionId = null;
        unset($_COOKIE[$this->sessionId]);
    }
    
    /**
     * setcookie wrapper
     *
     * @param void
     * @return void
     */
    protected function _setCookie($value, $expire) {
        $server = Server::getInstance();
        $file = null;
        $line = null;
        if (headers_sent($file, $line)) {
            $msg = 'Headers already sent in ' . $file . ' line ' . $line;
            throw new Exception($msg);
        }
        if (!setcookie($this->sessionName, $value, $expire, '/', $server->HTTP_HOST, false, true)) {
            $msg = 'Unable to set cookie';
            throw new Exception($msg);
        }
    }
    
    /**
     * редирект после авторизации через форму
     *
     * @param
     *            void
     * @return void
     */
    protected function redirect() {
        header ('Location: ' . Server::getInstance()->REQUEST_URI);
        // exit();
    }

    /**
     * Сохранить данные сессии
     *
     * @param
     *            void
     * @return boolean
     */
    protected function createSession() {
        if (!($this->currentUser instanceof User)) {
            $msg = 'Trying to save session without current user';
            throw new Exception($msg);
        }
        $this->clean();
        $db = Db::getInstance();
        $sql = 'INSERT INTO `' . $this->table . '` (
                `user_id`,
                `session_id`,
        
                `ip_address`,
                `http_user_agent`,
                `http_accept`,
                `http_accept_language`,
                `http_accept_encoding`,

                `hash_ip_address`,
                `hash_http_user_agent`,
                `hash_http_accept`,
                `hash_http_accept_language`,
                `hash_http_accept_encoding`,

                `created`,
                `last_logged_in`
                ) VALUES (
                ' . $db->qt($this->currentUser->id) . ',
                ' . $db->qt($this->sessionId) . ',

                ' . $db->qt($this->ipAddress). ',
                ' . $db->qt($this->httpUserAgent). ',
                ' . $db->qt($this->httpAccept). ',
                ' . $db->qt($this->httpAcceptLanguage). ',
                ' . $db->qt($this->httpAcceptEncoding). ',

                ' . $db->qt($this->hashIpAddress). ',
                ' . $db->qt($this->hashHttpUserAgent). ',
                ' . $db->qt($this->hashHttpAccept). ',
                ' . $db->qt($this->hashHttpAcceptLanguage). ',
                ' . $db->qt($this->hashHttpAcceptEncoding). ',
                        
                NOW(),
                NOW())';
        $db->query($sql);
        return (boolean)$db->affected_rows;
    }

    /**
     * Загрузить данные сессии
     *
     * @param void
     * @return array|false
     */
    protected function loadSessionData() {
        $db = Db::getInstance();
        if (! $this->sessionId) {
            return false;
        }
        $sql = 'SELECT * FROM `' . $this->table . '`
                WHERE `session_id` = ' . $db->qt($this->sessionId) . '
                LIMIT 1';
        $result = $db->query($sql);
        if (! $result->num_rows) {
            return false;
        }
        return $result->fetch_assoc();
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param multitype $value
     * @return Section
     * @throws Exception
     */
    public function __set($name, $value) {
        $msg = 'Undefined or readonly property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }
    
    /**
     * setter
     *
     * @param string $name
     * @return Ambigous <NULL, multitype:>
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (in_array($getter, get_class_methods($this))) {
            return $this->$getter($name);
        }
        $msg = 'Undefined property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }
    
    /**
     * Очистить старые или ошибочные сессии
     *
     * @param void
     * @return void
     */
    protected function clean() {
        $db = Db::getInstance();
        $sql = 'DELETE FROM `' . $this->table . '`
                WHERE `last_logged_in` < DATE_SUB(NOW(),
                    INTERVAL ' . $db->es($this->sessionLifetime) . ' SECOND)';
        $result = $db->query($sql);
        $sql = 'DELETE FROM `' . $this->table . '`
                WHERE `session_id` = ""';
        $result = $db->query($sql);
        $sql = 'DELETE FROM `' . $this->table . '`
                WHERE `active` != 1
                AND `last_logged_in` < DATE_SUB(NOW(),
                    INTERVAL 30 SECOND)';
        $result = $db->query($sql);
    }
    
    public static function currentUser() {
        if (self::instanceExists()) {
            return self::getInstance()->currentUser;
        } else {
            return null;
        }
    }
    
    public static function currentUserId() {
        $u = self::currentUser();
        return $u ? $u->id : null;
    }
}