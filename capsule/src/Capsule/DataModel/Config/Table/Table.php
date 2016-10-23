<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.12.2013 1:37:41 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Table;

use Capsule\DataModel\Config\Table\Columns\Columns;
use Capsule\DataModel\Config\Table\Indexes\Indexes;
use Capsule\DataModel\Config\AbstractConfig;
use Capsule\Capsule;
use Capsule\Db\Db;
use Capsule\Core\Fn;

/**
 * Table.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Indexes $indexes Indexes collection
 * @property Columns $columns Columns collection
 * @property string $engine Storage engine
 * @property string $autoIncrement Start auto increment
 * @property string $defaultCharset Character set
 * @property string $comment Table comment
 * @property string $name Table name
 */
class Table extends AbstractConfig
{
    /**
     * Default storage engine
     *
     * @var string
     */
    const DEFAULT_ENGINE = 'InnoDB';

    /**
     * Default character set
     *
     * @var string
     */
    const DEFAULT_CHARSET = 'utf8';

    /**
     * Default auto increment initial value
     *
     * @var string
     */
    const DEFAULT_AUTO_INCREMENT = '1';

    /**
     * String formatting indent
     *
     * @var int
     */
    const DEFAULT_INDENT = 4;

    /**
     * special properties
     *
     * @var string
     */
    const COLUMNS = 'columns',
          INDEXES = 'indexes';

    /**
     * @param void
     * @return self
     */
    public function __construct(array $data) {
        parent::__construct($data);
        if (array_key_exists(self::COLUMNS, $this->data)) {
            $this->data[self::COLUMNS] =
                new Columns($this->data[self::COLUMNS]);
        }
        if (array_key_exists(self::INDEXES, $this->data)) {
            $this->data[self::INDEXES] = new Indexes($this->data[self::INDEXES]);
        }
        $db = Db::getInstance();
        if (!$db->tableExists($this->name)) {
            $sql = $this->toString();
            $db->query($sql);
            if (!$db->tableExists($this->name, true)) {
                $msg = 'Unable to create table: ' . $this->name;
                throw new Exception($msg);
            }
        } else {
            $db->dropIfEmpty($this->name, true);
            $sql = $this->toString();
            $db->query($sql);
            if (!$db->tableExists($this->name, true)) {
                $msg = 'Unable to create table: ' . $this->name;
                throw new Exception($msg);
            }
        }
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . $this->name . '` (';
        $tmp[] = $this->columns->toString(self::DEFAULT_INDENT);
        if (isset($this->indexes)) {
            $tmp[] = $this->indexes->toString(self::DEFAULT_INDENT);
        }
        $sql[] = Fn::join_ne(',' . chr(10), $tmp);
        $sql[] = ') ENGINE=' . $this->get('engine', self::DEFAULT_ENGINE)
                . ' AUTO_INCREMENT=' .
                    $this->get('autoIncrement', self::DEFAULT_AUTO_INCREMENT)
                . ' DEFAULT CHARSET=' .
                    $this->get('defaultCharset', self::DEFAULT_CHARSET);
        $sql = join(chr(10), $sql);
        if (isset($this->comment) && $this->comment) {
            $sql.= ' COMMENT="' . $this->comment . '"';
        }
        return $sql;
    }
}