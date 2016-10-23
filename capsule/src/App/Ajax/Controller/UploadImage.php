<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 14.06.2014 8:15:47 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Ajax\Controller;

use App\Ajax\Controller\Controller;
use App\Cms\Cms;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\DataStruct\ReturnValue;
use Capsule\Superglobals\Post;
use Capsule\I18n\I18n;
use Capsule\File\Upload\Msg;
use Capsule\Plugin\Storage\Storage;
use App\Cms\Model\HistoryUploadImage;

/**
 * UploadImage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class UploadImage extends Controller
{
    protected $result;
    
    /**
     * Загрузить одно изображение
     * 
     * @param void
     * @return void
     */
    protected function uploadSingleImage() {
        $path = new Path(array(
            Capsule::getInstance()->systemRoot,
            Cms::getInstance()->config->templates,
            '/ajax/' . __FUNCTION__ . '.php')
        );
        include $path;
    }
    
    /**
     * Обработчик загрузки изображений вызываемый из ajax ответа шаблона
     * 
     * @param void
     * @return \Capsule\DataStruct\ReturnValue
     */
    private function uploadSingleImageHandler() {
        $this->result = new ReturnValue();
        $this->result->error = null;
        $this->retrieveImage();
        return $this->result;
    }
    
    /**
     * Возвращает изображение
     * 
     * @param void
     * @return resource
     */
    private function retrieveImage() {
        $image = $this->getPastedImage();
        if (!is_array($image)) {
            if ($this->result->error) {
                return false;
            }
            $image = $this->getUploadedImage();
            if (!is_array($image)) {
                if (!$this->result->error) {
                    $this->result->error = I18n::_('Unable to upload file');
                }
                return false;
            }
        }
        $image = $this->resize($image);
        $image = $this->crop($image);
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
            $this->result->error = I18n::_('Unable to save image');
            return false;
        }
        try {
            $result = Storage::getInstance()->addFile($path, $image['extension']);
        } catch (\Exception $e) {
            $this->result->error = I18n::_('Unable to save file');
            return false;
        }
        $image = array_replace($image, $result); 
        imagedestroy($image['image']);
        unset($image['image']);
        $history = new HistoryUploadImage();
        foreach ($image as $k => $v) {
            $this->result->$k = $v;
            $history->$k = $v;
        }
        $history->path = $history->url;
        $history->storage = Storage::getInstanceName(Storage::getInstance());
        HistoryUploadImage::deleteSamePath($history); 
        $history->store();
        $this->result->historyId = $history->id;
    }
    
    /**
     * Возвращает изображение, вставленное из буфера обмена в строку.
     * 
     *  @param void
     *  @return array|false
     */
    private function getPastedImage() {
        $image_string = Post::getInstance()->get('imageString');
        if (is_null($image_string)) {
            return false;
        }
        if (!is_scalar($image_string)) {
            return false;
        }
        if (!$image_string) {
            return false;
        }
        $image_string = base64_decode($image_string);
        if (!$image_string) {
            $this->result->error = I18n::_('Unable to decode image string');
            return false;
        }
        /**
         * Сначала создаем изображение поддерживаеиого типа а потом уже
         * выполняем getimagesize.
         * см. http://habrahabr.ru/post/224351/
         */
        $image = imagecreatefromstring($image_string);
        if (!is_resource($image)) {
            $this->result->error = I18n::_('Unable to load image');
            return false;
        }
        $img_info = getimagesizefromstring($image_string);
        if (false === $img_info) {
            $this->result->error = I18n::_('Unable to read image');
            return false;
        }
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width !== $img_info[0]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if ($height !== $img_info[1]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if (!$width) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (!$height) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (image_type_to_mime_type(IMAGETYPE_PNG) !== $img_info['mime']) {
            $this->result->error = I18n::_('Image type unsupported error');
            return false;
        }
        imagealphablending($image, false);
        imagesavealpha($image, true);
        return array(
            'image' => $image,
            'width' => $width,
            'height' => $height,
            'mime' => $img_info['mime'],
            'extension' => 'png',
            'name' => 'clipboard.png'
        );
    }
    
    /**
     * Возвращает изображение, загруженное из файла.
     *
     *  @param void
     *  @return array|false
     */
    private function getUploadedImage() {
        if (!isset($_FILES['file'])) {
            return false;
        }
        $file = $_FILES['file'];
        $keys = array(
            'name',
            'type',
            'tmp_name',
            'error',
            'size'
        );
        foreach ($keys as $key) {
            if (!isset($file[$key])) {
                $this->result->error = I18n::_('No file specified');
                return false;
            }
            if (!is_scalar($file[$key])) {
                $this->result->error = I18n::_('No file specified');
                return false;
            }
        }
        $name = $file['name'];
        $type = $file['type'];
        $tmp_name = $file['tmp_name'];
        $error = $file['error'];
        $size = $file['size'];
        if ($error) {
            $this->result->error = I18n::_(Msg::msg($error));
            return false;
        }
        if (!is_uploaded_file($tmp_name)) {
            $this->result->error = I18n::_('Unable to upload file');
            return false;
        }
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if (!$extension) {
            $this->result->error = I18n::_('File without extension');
            return false;
        }
        $extension = strtolower($extension);
        $type_to_extension = array(
            'jpg'  => 'jpg',
            'jpeg' => 'jpg',
            'png'  => 'png',
            'gif'  => 'gif',
        );
        if (!array_key_exists($extension, $type_to_extension)) {
            $this->result->error = I18n::_('Unsupported extension');
            return false;
        }
        $extension = $type_to_extension[$extension];
        $image = null;
        /**
         * Сначала создаем изображение поддерживаеиого типа а потом уже
         * выполняем getimagesize.
         * см. http://habrahabr.ru/post/224351/
         */
        switch ($extension) {
            case 'jpg':
                $image = @imagecreatefromjpeg($tmp_name); 
                break;
            case 'png':
                $image = @imagecreatefrompng($tmp_name);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            case 'gif':
                $image = @imagecreatefromgif($tmp_name);
                break;
            default:
                $this->result->error = I18n::_('Unsupported extension');
                return false;
                break;
        }
        if (!is_resource($image)) {
            $this->result->error = I18n::_('Unable to load image');
            return false;
        }
        $img_info = getimagesize($tmp_name);
        if (false === $img_info) {
            $this->result->error = I18n::_('Unable to read image');
            return false;
        }
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width !== $img_info[0]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if ($height !== $img_info[1]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if (!$width) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (!$height) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (image_type_to_mime_type(IMAGETYPE_PNG) !== $img_info['mime'] &&
            image_type_to_mime_type(IMAGETYPE_JPEG) !== $img_info['mime'] &&
            image_type_to_mime_type(IMAGETYPE_GIF) !== $img_info['mime']) {
            $this->result->error = I18n::_('Image type unsupported error');
            return false;
        }
        return array(
            'image' => $image,
            'width' => $width,
            'height' => $height,
            'mime' => $img_info['mime'],
            'extension' => $extension,
            'name' => $name
        );
    }
    
    /**
     * Resize image
     * 
     * @param array $image
     * @return array
     */
    private function resize(array $image) {
        $keys = array('width', 'height');
        $post = Post::getInstance();
        foreach ($keys as $key) {
            $$key = $post->$key;
            if (!is_scalar($$key)) return $image;
            if (!ctype_digit($$key)) return $image;
            $$key = intval($$key);
            if (!$$key) return $image;
        }
        $width = ${'width'};
        $height = ${'height'};
        if ($width === $image['width'] && $height === $image['height']) return $image;
        $k = 1.7; // Коэффициент пошагового увеличения
        $tmp_width = intval($image['width'] * $k);
        if ($tmp_width > $width) {
            $tmp_width = $width;
        }
        $tmp_height = intval($image['height'] * $k);
        if ($tmp_height > $height) {
            $tmp_height = $height;
        }
        $src_img = $image['image'];
        if ('gif' === $image['extension']) {
            $tmp_width = $width;
            $tmp_height = $height;
        }
        while ($tmp_height < $height || $tmp_width < $width) {
            $tmp_img = imagecreatetruecolor($tmp_width, $tmp_height);
            if ('png' === $image['extension']) {
                imagealphablending($tmp_img, false);
                imagesavealpha($tmp_img, true);
                $src_trans = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
                imagefill($tmp_img, 0, 0, $src_trans);
            } elseif ('gif' === $image['extension']) {
                $src_trans = imagecolortransparent($src_img);
                if ($src_trans != (-1)) {
                    $transparent_color = ImageColorsForIndex($src_img, $src_trans);
                }
                if (!empty($transparent_color)) { /* simple check to find wether transparent color was set or not */
                    $transparent_new = ImageColorAllocate($tmp_img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagefill($tmp_img, 0, 0, $transparent_new ); /* don't forget to fill the new image with the transparent color */
                    $transparent_new_index = ImageColorTransparent($tmp_img, $transparent_new);
                }
            }
            if (false === imagecopyresampled($tmp_img, $src_img, 0, 0, 0, 0, $tmp_width, $tmp_height, $image['width'], $image['height'])) {
                imagedestroy($tmp_img);
                return $image;
            }
            $image['width'] = $tmp_width;
            $image['height'] = $tmp_height;
            $src_img = $tmp_img;
            $tmp_width = intval($image['width'] * $k);
            if ($tmp_width > $width) {
                $tmp_width = $width;
            }
            $tmp_height = intval($image['height'] * $k);
            if ($tmp_height > $height) {
                $tmp_height = $height;
            }
        }
        $dst_image = imagecreatetruecolor($width, $height);
        if ('png' === $image['extension']) {
            imagealphablending($dst_image, false);
            imagesavealpha($dst_image, true);
            $src_trans = imagecolorallocatealpha($dst_image, 255, 255, 255, 127);
            imagefill($dst_image, 0, 0, $src_trans);
        } elseif ('gif' === $image['extension']) {
            $src_trans = imagecolortransparent($src_img);
            if ($src_trans != (-1)) {
                $transparent_color = ImageColorsForIndex($src_img, $src_trans);
            }
            if (!empty($transparent_color)) { /* simple check to find wether transparent color was set or not */
                $transparent_new = ImageColorAllocate($dst_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($dst_image, 0, 0, $transparent_new); /* don't forget to fill the new image with the transparent color */
                $transparent_new_index = ImageColorTransparent($dst_image, $transparent_new);
            }
        }
        if (false === imagecopyresampled($dst_image, $src_img, 0, 0, 0, 0, $tmp_width, $tmp_height, $image['width'], $image['height'])) {
            imagedestroy($dst_image);
            return $image;
        }
        imagedestroy($image['image']);
        $image['image'] = $dst_image;
        $image['width'] = $width;
        $image['height'] = $height;
        return $image;
    }
    
    /**
     * Crop image
     *
     * @param array $image
     * @return array
     */
    private function crop(array $image) {
        $keys = array('x1', 'y1', 'x2', 'y2');
        $post = Post::getInstance();
        foreach ($keys as $key) {
            $$key = $post->$key;
            if (!is_scalar($$key)) return $image;
            if (!ctype_digit($$key)) return $image;
            $$key = intval($$key);
            if (!$$key && 0 !== $$key) return $image;
        }
        $x1 = ${'x1'};
        $y1 = ${'y1'};
        $x2 = ${'x2'};
        $y2 = ${'y2'};
        $src_width = $image['width'];
        $src_height = $image['height'];
        if ($x1 >= $src_width) return $image;
        if ($x2 >= $src_width) return $image;
        if ($y1 >= $src_height) return $image;
        if ($y2 >= $src_height) return $image;
        $dst_width = abs($x2 - $x1) + 1;
        $dst_height = abs($y2 - $y1) + 1;
        if ($x1 + $dst_width > $src_width) return $image;
        if ($y1 + $dst_height > $src_height) return $image;
        
        
        $dst_image = imagecreatetruecolor($dst_width, $dst_height);
        if ('png' === $image['extension']) {
            imagealphablending($dst_image, false);
            imagesavealpha($dst_image, true);
            $src_trans = imagecolorallocatealpha($dst_image, 255, 255, 255, 127);
            imagefill($dst_image, 0, 0, $src_trans);
        } elseif ('gif' === $image['extension']) {
            $src_trans = imagecolortransparent($image['image']);
            imagepalettecopy($dst_image, $image['image']);
            imagefill($dst_image, 0, 0, $src_trans);
            imagecolortransparent($dst_image, $src_trans);
        }
        if (false === imagecopyresampled($dst_image, $image['image'], 0, 0, $x1, $y1, $dst_width, $dst_height, $dst_width, $dst_height)) {
            imagedestroy($dst_image);
            return $image;
        }
        imagedestroy($image['image']);
        $image['image'] = $dst_image;
        $image['width'] = $dst_width;
        $image['height'] = $dst_height;
        return $image;
    }
    
    /**
     * Меняет значение favorites на противоположное
     * 
     * @param void
     * @return void
     */
    protected function toggleFavoritesImage() {
        $ret = array(
            'error' => true
        );
        $image = HistoryUploadImage::id(Post::getInstance()->gets('id'));
        if ($image) {
            $k = 'favorites';
            $v = $image->$k;
            $image->$k = !$v;
            $image->store();
            $ret[$k] = !$v;
            $ret['error'] = false;
        }
        print json_encode($ret);
    }
    
    /**
     * Устанавливает значение favorites в true
     *
     * @param void
     * @return void
     */
    protected function addToFavoritesImage() {
        $ret = array(
            'error' => true
        );
        $image = HistoryUploadImage::id(Post::getInstance()->gets('id'));
        if ($image) {
            $image->favorites = true;
            $image->store();
            $ret['error'] = false;
        }
        print json_encode($ret);
    }
    
    /**
     * Добавляет комментарий к изображению
     * 
     * @param void
     * @return void
     */
    protected function commentImage() {
        $ret = array(
            'error' => true
        );
        $image = HistoryUploadImage::id(Post::getInstance()->gets('id'));
        if ($image) {
            $image->comment = Post::getInstance()->gets('comment');
            $image->store();
            $ret['error'] = false;
        }
        print json_encode($ret);
    }
    
    /**
     * Удаляет изображение из истории и из хранилища, если хранилищем 
     * поддерживается функция удаления
     * 
     * @param void
     * @return void
     */
    protected function deleteImage() {
        $ret = array(
            'error' => true
        );
        $image = HistoryUploadImage::id(Post::getInstance()->gets('id'));
        if ($image) {
            $filename = pathinfo($image->path, PATHINFO_BASENAME);
            try {
                Storage::getInstance()->delFile($filename);
            } catch (\Exception $e) {
            }
            HistoryUploadImage::del($image->id);
            $ret['error'] = false;
        }
        print json_encode($ret);
    }
}