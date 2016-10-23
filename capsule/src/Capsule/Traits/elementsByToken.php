<?php
namespace Capsule\Traits;
use Capsule\Db\Db;
trait elementsByToken
{
    public static function elementsByToken($token) {
        $db = Db::getInstance();
        $table = $db->bq(self::config()->table->name);
        $sql = 'SELECT *
                FROM ' . $table . '
                WHERE `token` = ' . $db->qt($token);
        return self::populate($db->query($sql));
    }
}