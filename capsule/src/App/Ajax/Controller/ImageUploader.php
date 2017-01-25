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


use App\Cms\Model\HistoryUploadImage;
use Capsule\File\Upload\Msg;
use Capsule\Plugin\Storage\Storage;
use Capsule\User\Auth;

/**
 * Class ImageUploader
 * @package App\Ajax\Controller
 */
class ImageUploader extends Controller
{
    const FILES_VAR_NAME = 'file';

    /**
     *
     */
    public function handle()
    {
        if (!Auth::getInstance()->user()) {
            return;
        }
        $this->receive();
        print json_encode([
            'status' => 'ok'
        ]);
    }

    /**
     *
     */
    protected function receive()
    {
        if (!isset($_FILES[static::FILES_VAR_NAME])) {
            throw new \Exception('the variable is not passed');
        }
        $file = $_FILES[static::FILES_VAR_NAME];
        $name = $file['name'];
        $type = $file['type'];
        $size = $file['size'];
        $path = $file['tmp_name'];
        $error = $file['error'];

        if ($error) {
            throw new \Exception('Upload error: ' . Msg::msg($error));
        }

        if (!is_uploaded_file($path)) {
            throw new \Exception('File was not uploaded');
        }

        $extension = strtolower(substr(strrchr($name, "."), 1));
        $tmp_path = $path;

        switch ($extension) {
            case 'png' :
                $image = imagecreatefrompng($tmp_path);
                $img_info = getimagesize($tmp_path);
                break;
            case 'gif' :
                $image = imagecreatefromgif($tmp_path);
                $img_info = getimagesize($tmp_path);
                break;
            case 'jpg' :
                $image = imagecreatefromjpeg($tmp_path);
                $img_info = getimagesize($tmp_path);
                break;
            case 'jpeg' :
                $image = imagecreatefromjpeg($tmp_path);
                $img_info = getimagesize($tmp_path);
                break;
            default :
                throw new \Exception('unsupported file type');
                break;
        }

        /**
         * Сначала создаем изображение поддерживаеиого типа а потом уже
         * выполняем getimagesize.
         * см. http://habrahabr.ru/post/224351/
         */
        if (!is_resource($image)) {
//            try {
//                $imagick = new \Imagick($tmp_path);
//            } catch (\ImagickException $e) {
//                Tools::dump($image_string);
//            }

            if (!is_resource($image)) {
                throw new \Exception('Unable to create image from source data');
            }
        }
        if (false === $img_info) {
            throw new \Exception('Unable to get image size from source data');
        }
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width !== $img_info[0]) {
            throw new \Exception('Wrong width');
        }
        if ($height !== $img_info[1]) {
            throw new \Exception('Wrong height');
        }
        if (!$width) {
            throw new \Exception('Empty width');
        }
        if (!$height) {
            throw new \Exception('Empty height');
        }
        switch (strtolower($img_info['mime'])) {
            case 'image/jpeg' :
                $extension = 'jpg';
                break;
            case 'image/png' :
                $extension = 'png';
                break;
            case 'image/gif' :
                $extension = 'gif';
                break;
            default :
                throw new \Exception('Unsupported mime');
        }
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $max_width = 1024;
        $max_height = 1024;
        $new_size = self::calcResize($width, $height, $max_width, $max_height);
        if ($new_size['width'] != $width || $new_size['height'] != $height) {
//            if (php_sapi_name() == "cli") {
//                echo Cs::_('resize image: ' . $width . 'x' . $height . ' to ' .
//                        $new_size['width'] . 'x' . $new_size['height'], 'blue', 'yellow') . PHP_EOL;
//            }
            $new_image = imagecreatetruecolor($new_size['width'], $new_size['height']);
            $white = imagecolorallocate($new_image, 255, 255, 255);
            imagefilledrectangle($new_image, 0, 0, $new_size['width'], $new_size['height'], $white);
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_size['width'], $new_size['height'], $width, $height);
            imagedestroy($image);
            $image = $new_image;
            $width = $new_size['width'];
            $height = $new_size['height'];
        }

        $image = array(
            'image' => $image,
            'width' => $width,
            'height' => $height,
            'mime' => $img_info['mime'],
            'name' => $name,
            'extension' => $extension
        );

        $tmp_handle = tmpfile();
        $meta = stream_get_meta_data($tmp_handle);
        $path = $meta['uri'];

        switch ($image['extension']) {
            case 'jpg':
                $result = imagejpeg($image['image'], $path);
                break;
            case 'gif':
                $result = imagegif($image['image'], $path);
                break;
            default:
                $result = imagepng($image['image'], $path, 9);
                break;
        }
        if (!$result) {
            throw new \Exception('Unable to create image');
        }
        try {
            $result = Storage::getInstance()->addFile($path, $image['extension']);
        } catch (\Exception $e) {
            throw new \Exception('Unable to store image: ' . $e->getMessage() . '[' . $e->getLine() . ']');
        }
        $image = array_replace($image, $result);
        imagedestroy($image['image']);
        unset($image['image']);
        $history = new HistoryUploadImage();
        foreach ($image as $k => $v) {
            $history->$k = $v;
        }
        $history->path = $history->url;
        $history->storage = Storage::getInstanceName(Storage::getInstance());
        HistoryUploadImage::deleteSamePath($history);
        $history->store();
    }

    /**
     * @param $width
     * @param $height
     * @param $max_width
     * @param $max_height
     * @return array
     * @throws \Exception
     */
    protected static function calcResize($width, $height, $max_width, $max_height)
    {
        if (is_null($max_width) && is_null($max_height)) {
            return array(
                'width' => $width,
                'height' => $height
            );
        }
        if (is_null($max_height)) {
            if ($width <= $max_width) {
                return array(
                    'width' => $width,
                    'height' => $height
                );
            }
            $new_height = round($height * ($max_width / $width));
            return array(
                'width' => $max_width,
                'height' => $new_height
            );
        }
        if (is_null($max_width)) {
            if ($height <= $max_height) {
                return array(
                    'width' => $width,
                    'height' => $height
                );
            }
            $new_width = round($width * ($max_height / $height));
            return array(
                'width' => $max_width,
                'height' => $new_width
            );
        }
        if ($width <= $max_width && $height <= $max_height) {
            return array(
                'width' => $width,
                'height' => $height
            );
        }
        if ($width > $max_width) {
            $new_height = round($height * ($max_width / $width));
            if ($new_height <= $max_height) {
                return array(
                    'width' => $max_width,
                    'height' => $new_height
                );
            }
        }
        if ($height > $max_width) {
            $new_width = round($width * ($max_height / $height));
            if ($new_width <= $max_width) {
                return array(
                    'width' => $new_width,
                    'height' => $max_height
                );
            }
        }
        throw new \Exception('unable to calc resize');
    }
}