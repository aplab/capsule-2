<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 04.12.2016
 * Time: 21:29
 */

namespace App\Cms\Ui\DataModel\ObjectEditor;


use Capsule\DataModel\DataModel;
use Capsule\Tools\ClassTools\AccessorName;

class ObjectEditor
{
    use AccessorName;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * ObjectEditor constructor.
     * @param DataModel $model
     * @param $instance_name
     */
    public function __construct(DataModel $model, string $instance_name)
    {
        $this->data['model'] = $model;
        $this->data['config'] = $model->config();
        $this->data['instanceName'] = $instance_name;
        $this->configure();
    }

    protected function configure()
    {

    }
}