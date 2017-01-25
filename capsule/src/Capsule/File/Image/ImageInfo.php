<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 31.08.2014 21:46:25 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\File\Image;

use Capsule\Common\Path;
/**
 * ImageInfo.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class ImageInfo implements \JsonSerializable
{
    protected $path;
    
    protected $width;
    
    protected $height;
    
    protected $mime;
    
    protected $extension;
    
    protected $type;
    
    public function __construct($path, $extensoin = null) {
        $path = new Path($path);
        if (!file_exists(strval($path))) {
            $msg = 'File not found: ' . $path;
            throw new \Exception($msg);
        }
        $tmp = getimagesize(strval($path));
        if (!$tmp) {
            $msg = 'Unable to access this image: ' . $path;
            throw new \Exception($msg);
        }
        $this->width = $tmp[0];
        $this->height = $tmp[1];
        if (!($this->width && $this->height)) {
            $msg = 'ImageInfo is empty: ' . $path;
            throw new \Exception($msg);
        }
        $this->type = strtolower(image_type_to_extension($tmp[2], false));
        $type_to_extension = array(
            'jpg'  => 'jpg',
            'jpeg' => 'jpg',
            'png'  => 'png',
            'gif'  => 'gif',
        );
        $this->extension = array_key_exists($this->type, $type_to_extension) ? $type_to_extension[$this->type] : null;
        $this->mime = $tmp['mime'];
        if (!$this->extension) {
            $msg = 'Unsupported extension or empty: ' . $path;
            throw new \Exception($msg);
        }
        $this->path = new Path($path);
    }
    
    public function toArray() {
        return array(
            'path' => $this->path . '',
            'width' => $this->width,
            'height' => $this->height,
            'mime' => $this->mime,
            'extension' => $this->extension,
            'type' => $this->type
        );
    }
    
    public function jsonSerialize() {
        return $this->toArray();
    }
    
    public function toJson($options = null) {
        if (is_null($options)) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        }
        return json_encode($this->toArray(), $options);
    }
}