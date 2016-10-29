<?php
/**
 * Created by PhpStorm.
 * User: polyanin
 * Date: 16.10.2016
 * Time: 14:34
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
include dirname(__DIR__, 3) . '/capsule/src/Capsule/Capsule.php';
$system = \Capsule\Capsule::getInstance(dirname(__DIR__, 2));
//\Capsule\Component\DataStorage\DataStorage::getInstance()->destroy();
//$app_manager = \App\AppManager::getInstance();
//$app = $app_manager->selectApp();
//\Capsule\Tools\Tools::dump($app);
//\Capsule\Tools\Tools::dump(\Capsule\Ui\Section::templatesDir()->createDir());
//\Capsule\Tools\Tools::dump(\App\Cms\Ui\Section::templatesDir()->createDir());
//$p = new \Capsule\Component\Path\ComponentTemplatePath('\App\Cms\Ui\Section', 'abstract');
//\Capsule\Tools\Tools::dump($p->createFile());

class a
{
    use \Capsule\Tools\ClassTools\AccessorName;
    private function getA()
    {

    }

    private function getB()
    {

    }
    public function __get($name)
    {
        $m = static::_getter($name);
        if ($m) {
            $this->$m($name);
        }
    }

    public function __set($name, $value)
    {
        $m = static::_setter($name);
        if ($m) {
            $this->$m($name, $value);
        }
    }
}

class b
{
    use \Capsule\Tools\ClassTools\AccessorName;
    private function getC()
    {

    }

    private function getD()
    {

    }
    public function __get($name)
    {
        $m = static::_getter($name);
        if ($m) {
            $this->$m($name);
        }
    }

    public function __set($name, $value)
    {
        $m = static::_setter($name);
        if ($m) {
            $this->$m($name, $value);
        }
    }
}
$a = [];
for ($i = 0; $i < 10; $i++) {
    $a[$i] = new a;
    $a[$i]->d;
}
\Capsule\Tools\Tools::dump($a);
$b = [];
for ($i = 0; $i < 10; $i++) {
    $b[$i] = new b;
    $b[$i]->d;
    $b[$i]->t=2;
}
\Capsule\Tools\Tools::dump($b);
\Capsule\Tools\Tools::dump($system->worktime);