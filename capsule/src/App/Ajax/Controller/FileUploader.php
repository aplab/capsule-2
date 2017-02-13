<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 26.01.2017
 * Time: 0:10
 */

namespace App\Ajax\Controller;


use App\Cms\Plugin\UserFiles\File;
use Capsule\Db\Db;
use Capsule\File\Upload\Msg;
use Capsule\Plugin\Storage\Storage;
use Capsule\User\Auth;

/**
 * Class ImageUploader
 * @package App\Ajax\Controller
 */
class FileUploader extends Controller
{
    /**
     *
     */
    const FILES_VAR_NAME = 'file';

    /**
     *
     */
    public function handle()
    {
        if (!Auth::getInstance()->user()) {
            return;
        }
        $url = $this->receive();
        print json_encode([
            'status' => 'ok',
            'url' => $url
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function receive()
    {
        if (!isset($_FILES[static::FILES_VAR_NAME])) {
            throw new \Exception('the variable is not passed');
        }
        $file = $_FILES[static::FILES_VAR_NAME];
        $keys = array('name', 'type', 'size', 'tmp_name', 'error');
        foreach ($keys as $key) {
            if (!isset($file[$key])) {
                throw new \Exception('the variable is not passed');
            }
            if (!is_scalar($file[$key])) {
                throw new \Exception('wrong type of variable');
            }
        }

        $name = $file['name'];
        $type = $file['type'];
        $size = $file['size'];
        $path = $file['tmp_name'];
        $error = $file['error'];

        if ($error) {
            throw new \Exception('upload error ' . Msg::msg($error));
        }

        if (!is_uploaded_file($path)) {
            throw new \Exception('file was not uploaded');
        }

        $extension = strtolower(substr(strrchr($name, "."), 1));
        try {
            $result = Storage::getInstance()->addFile($path, $extension);
        } catch (\Exception $e) {
            throw new \Exception('Unable to store image');
        }
        $filename = pathinfo($result['pathname'], PATHINFO_BASENAME);

        $db = Db::getInstance();
        $sql = 'DELETE FROM `' . File::config()->table->name . '`
                WHERE `filename` =  ' . $db->qt($filename);
        $db->query($sql);

        $o_file = new File();
        $o_file->name = $name;
        $o_file->filename = $filename;
        $o_file->contentType = $type;
        $o_file->store();
        $id = $o_file->id;
        return $o_file->directLink;
    }
}