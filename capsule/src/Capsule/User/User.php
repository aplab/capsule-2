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

use Capsule\Component\Utf8String;
use Capsule\Exception;
use Capsule\Db\Db;
use Capsule\Capsule;
use Capsule\Model\IdBased;
/**
 * User.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $login
 * @property string $password
 */
class User extends IdBased
{
    /**
     * Минимальная длина пароля.
     *
     * @var int
     */
    const PASSWORD_MIN_LENGTH = 8;

    /**
     * @param $value
     * @param $name
     * @return $this
     * @throws Exception
     */
    protected function setPassword($value, $name)
    {
        if (!$value && array_key_exists($name, $this->data) && $this->data[$name]) {
            // I do not want to change the password
            return $this;
        }
        if (self::PASSWORD_MIN_LENGTH > Utf8String::length($value)) {
            $msg = 'Wrong password length';
            throw new Exception($msg);
        }
        $hash = password_hash($value, PASSWORD_DEFAULT);
        if (!$hash) {
            $msg = 'Password hash error';
            throw new Exception($msg);
        }
        $this->data[$name] = $hash;
        return $this;
    }

    /**
     * Проверка пароля.
     *
     * @param string $password
     * @return boolean
     */
    public function password($password)
    {
        $hash = $this->password;
        if (password_verify($password, $hash)) {
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $this->password = $password;
                $this->store();
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $login
     * @return self
     */
    public static function getElementByLogin($login)
    {
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
    public static function createDefaultUser()
    {
        $user = new self;
        $user->login = Capsule::getInstance()->config->defaultUser->login;
        $user->password = Capsule::getInstance()->config->defaultUser->password;
        $user->store();
        return $user;
    }
}