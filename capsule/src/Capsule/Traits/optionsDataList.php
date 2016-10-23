<?php
namespace Capsule\Traits;
use Capsule\Db\Db;
trait optionsDataList
{
    public static function optionsDataList() {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $properties = $class::config()->properties;
            if (isset($properties->name)) {
                $pname = 'name';
            } else {
                $pname = $class::$key;
            }
            $db = Db::getInstance();
            $table = $db->bq(self::config()->table->name);
            $sql = 'SELECT `id` AS `value`, 
                           `' . $pname . '` AS `text`, 
                            FALSE AS `selected`
                            FROM ' . $table . '
                            ORDER BY `id` ASC';
            self::$common[$class][__FUNCTION__] =
                $db->query($sql)->fetch_all_index('value', null);
        }
        return self::$common[$class][__FUNCTION__];
    }
}