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


use Capsule\Core\Fn;
use Capsule\DataModel\Config\Config;
use Capsule\DataModel\Config\Properties\FormElement;
use Capsule\DataModel\DataModel;
use Capsule\Tools\ClassTools\AccessorName;
use Capsule\Tools\Tools;

/**
 * Class ObjectEditor
 * @package App\Cms\Ui\DataModel\ObjectEditor
 * @property Config $config
 */
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
     * @param $instance_name
     * @return ObjectEditor|null
     */
    public function getInstance($instance_name)
    {
        return array_key_exists($instance_name, static::$instances) ? static::$instances[$instance_name] : null;
    }

    /**
     * ObjectEditor constructor.
     * @param DataModel $model
     * @param string $instance_name
     * @throws Exception
     */
    public function __construct(DataModel $model, string $instance_name)
    {
        if (array_key_exists($instance_name, static::$instances)) {
            throw new Exception('Instance already exists: ' . $instance_name);
        }
        $this->data['model'] = $model;
        $this->data['config'] = $model->config();
        $this->data['instanceName'] = $instance_name;
        $this->data['elements'] = [];
        $this->data['groups'] = [];
        $this->configure();
        Tools::dump($this);
        die();
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $setter = static::_setter($name);
        if ($setter) {
            return $this->$setter($value, $name);
        }
        echo 'setter ';
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = static::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     *
     */
    protected function configure()
    {
        $element_ns = Fn::ns($this) . '\\Element';
        foreach ($this->config->properties as $property) {
            $form_element_list = $property->get('formElement');
            if (!is_array($form_element_list)) {
                continue;
            }
            foreach ($form_element_list as $form_element) {
                if (!($form_element instanceof FormElement)) {
                    continue;
                }
                if (!isset($form_element->order)) {
                    $form_element->order = 0;
                }
                $element_class = Fn::cc($form_element->type, $element_ns);
                $this->data['elements'][] = new $element_class(
                    $this->model,
                    $property,
                    $form_element
                );
            }
        }
        usort ($this->data['elements'], function($a, $b) {
            return $a->formElement->order <=> $b->formElement->order;
        });
    }
}