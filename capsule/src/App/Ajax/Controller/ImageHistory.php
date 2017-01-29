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
use Capsule\Tools\Tools;
use Respect\Validation\Validator;

/**
 * Class ImageHistory
 * @package App\Ajax\Controller
 */
class ImageHistory extends Controller
{
    /**
     *
     */
    protected function listItems()
    {
        print '[';
        foreach (HistoryUploadImage::history($this->_from(), 500) as $k => $item) {
            if ($k) {
                print ',' . PHP_EOL;
            }
            print json_encode($item);
        }
        print ']';
    }

    /**
     * @return int|mixed
     */
    private function _from()
    {
        $param = Ajax::getInstance()->param;
        $from = reset($param);
        if (Validator::digit()->validate($from)) {
            return $from;
        }
        return 0;
    }

    /**
     *
     */
    protected function dropItem()
    {
        $param = Ajax::getInstance()->param;
        $id = reset($param);
        if (!Validator::digit()->validate($id)) {
            return;
        }
        $o = HistoryUploadImage::id($id);
        if (!$o) {
            return;
        }
        if (HistoryUploadImage::del($id)) {
            print json_encode(new class() {
                var $status = 'ok';
            });
        }
    }

    /**
     *
     */
    protected function favItem()
    {
        $param = Ajax::getInstance()->param;
        $id = reset($param);
        if (!Validator::digit()->validate($id)) {
            return;
        }
        $o = HistoryUploadImage::id($id);
        if (!$o) {
            return;
        }
        $o->favorites = 1;
        $o->store();
        print json_encode(new class() {
            var $status = 'ok';
        });
    }
}