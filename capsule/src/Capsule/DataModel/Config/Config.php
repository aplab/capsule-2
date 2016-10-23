<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace Capsule\DataModel\Config;

use Capsule\DataModel\Config\Properties\Properties;
use Capsule\DataModel\Config\Table\Table;

/**
 * Config.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Table $table
 * @property Properties $properties
 */
class Config extends AbstractConfig
{
    /**
     * special properties
     *
     * @var string
     */
    const PROPERTIES = 'properties',
          TABLE = 'table',
          TAB_ORDER = 'tabOrder';

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        if (array_key_exists(self::PROPERTIES, $this->data)) {
            $this->data[self::PROPERTIES] =
                new Properties($this->data[self::PROPERTIES]);
        }
        if (array_key_exists(self::TABLE, $this->data)) {
            $this->data[self::TABLE] = new Table($this->data[self::TABLE]);
        }
        if (array_key_exists(self::TAB_ORDER, $this->data)) {
            $this->data[self::TAB_ORDER] = new TabOrder($this->data[self::TAB_ORDER]);
        }
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString()
    {
        return '';
    }
}