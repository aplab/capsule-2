<?php
/**
 * Created by Alexander Polyanin polyanin@gmail.com.
 * User: polyanin
 * Date: 26.06.2016
 * Time: 13:02
 */

namespace App\Cms\Plugin\UserFiles;


use Capsule\Component\Path\Path;
use Capsule\Db\Db;
use Capsule\Unit\NamedTsUsr;

class File extends NamedTsUsr
{
    /**
     * Возвращает страницу ообъектов из связанной таблицы
     *
     * @param int $page_number
     * @param int $items_per_page
     * @return array
     */
    public static function page($page_number = 1, $items_per_page = 10)
    {
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                ORDER BY `id` DESC
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name) {
        $getter = self::_getter($name);
        if ($getter) {
            return true;
        }
        return array_key_exists($name, $this->data);
    }

    protected function getLink()
    {
        $path = new Path(
            $this->config()->linkPrefix,
            $this->id,
            $this->name
        );
        return (string)$path;
    }

    protected function setLink()
    {

    }

    protected function getDirectLink()
    {
        $path_part = '/' . join('/', array_slice(str_split($this->filename, 3), 0, 3));
        $path = new Path(
            $this->config()->directLinkPrefix,
            $path_part,
            $this->filename
        );
        return (string)$path;
    }

    protected function setDirectLink()
    {

    }
}