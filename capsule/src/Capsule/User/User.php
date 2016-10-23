<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 23.01.2014 23:55:34 YEKT 2014                                              |
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

use Capsule\Common\String;
use Capsule\Exception;
use Capsule\Db\Db;
use Capsule\Capsule;
use Capsule\Module\Module;
/**
 * User.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class User extends Module
{
    /**
     * Минимальная длина пароля.
     *
     * @var int
     */
    const PASSWORD_MIN_LENGTH = 8;

    /**
     * Весовой параметр из двух цифр является двоичным логарифмом счетчика
     * итераций низлежащего хэширующего алгоритма, основанного на Blowfish, и
     * должен быть в диапазоне 04-31
     *
     * @var numeric string 04-31
     */
    const PASSWORD_COST = '07';

    protected function setPassword($value, $name) {
        if (!$value && array_key_exists($name, $this->data) &&
                $this->data[$name]) {
            // I do not want to change the password
            return $this;
        }
        $str = './' . join(array_merge(range('a','z'), range('A','Z'), range(0, 9)));
        $salt = substr(str_shuffle($str), 22);
        $pass = $value;
        if (self::PASSWORD_MIN_LENGTH > String::length($pass)) {
            $msg = 'Wrong password length';
            throw new Exception($msg);
        }
        $hash = crypt($pass, '$2a$' . self::PASSWORD_COST . '$' . $salt . '$');
        if (strlen($hash) < strlen($salt)) {
            $msg = 'Crypt error';
            throw new Exception($msg);
        }
        $this->data[$name] = $hash;
        return $this;
    }

    /**
     * Проверка пароля.
     *
     * @param unknown $password
     * @return boolean
     */
    public function password($password) {
        $hash = $this->password;
        return crypt(strval($password), $hash) === $hash;
    }

    /**
     * @param string $login
     * @return self
     */
    public static function getElementByLogin($login) {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $sql = 'SELECT * FROM `' . $table . '`
                WHERE `login` = ' . $db->qt($login);
        $objects = self::populate($db->query($sql));
        // array_shift returns NULL if array is empty
        return array_shift($objects);
    }
    
    /**
     * Создать пользователя по умолчанию для начального входа в систему
     *
     * @param void
     * @return self
     * @throws Exception
     */
    public static function createDefaultUser() {
        $user = new self;
        $user->login = Capsule::getInstance()->config->defaultUser->login;
        $user->password = Capsule::getInstance()->config->defaultUser->password;
        $user->store();
        return $user;
    }
}