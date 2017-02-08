<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 29.01.2017
 * Time: 1:32
 */

namespace App\Ajax\Controller;


use App\Ajax\Ajax;
use App\Cms\Model\HistoryUploadImage;
use Capsule\User\Auth;
use Respect\Validation\Validator;

/**
 * Class ImageHistory
 * @package App\Ajax\Controller
 */
class PropertyEditor extends Controller
{

    public function handle()
    {
        if (!Auth::getInstance()->user()) {
            return;
        }
        try {
            $class = $this->find_class();
            $id = $_POST['pk']['id'];
            Validator::digit()->check($id);
            $o = $class::id($id);
            $name = $_POST['name'];
            $value = $_POST['value'];
            $o->{$name} = $value;
            $o->store();
            print json_encode([
                'status' => 'ok',
                'value' => $o->$name
            ]);
        } catch (\Throwable $exception) {
            print json_encode([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    private function find_class()
    {
        $class = $_POST['class'];
        if (class_exists($class)) {
            return $class;
        }
        throw new \Exception('class not found');
    }
}