<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 17.10.2016
 * Time: 23:09
 */
/**
 * @package Capsule
 */

namespace Capsule\I18n;

use Capsule\Component\Json\Loader\Loader;
use Capsule\Component\Path\Path;
use Capsule\Component\Utf8String;
use Capsule\Core\Singleton;
use Capsule\Capsule;
use Capsule\Core\Fn;
/**
 * I18n.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class I18n extends Singleton
{
    /**
     * lang definition
     *
     * @var string
     */
    const   RU = 'ru',
            EN = 'en';
    
    /**
     * @var string
     */
    const EXTENSION = '.json';
    
    /**
     * Настройки перевода делать до создания экземпляра
     *
     * @var string
     */
    public static $lang = self::RU;

    /**
     * Internal data
     *
     * @var array
     */
    private $data = array();

    /**
     * I18n constructor.
     */
    protected function __construct()
    {
        $path = new Path(
            Capsule::getInstance()->systemRoot,
            Capsule::DIR_CONFIG,
            Fn::get_namespace($this)
        );
        if (!is_scalar(self::$lang)) {
            return;
        }
        $path = new Path($path, self::$lang . self::EXTENSION);
        if (!file_exists($path)) {
            return;
        }
        $loader = new Loader($path);
        try {
        	$this->data = array_change_key_case($loader->loadToArray(), CASE_LOWER);
        } catch (\Exception $e) {
            $this->data = array();
        }
    }
    
    /**
     * Translate
     *
     * @param string $text
     * @return string
     */
    public function __invoke($text)
    {
        $text = trim($text);
        $u = false;
        if (Utf8String::ucfirst($text) === $text) {
            $u = true;
        }
        $tmp = strtolower($text);
        return array_key_exists($tmp, $this->data)
            ? ($u ? Utf8String::ucfirst($this->data[$tmp])
                : $this->data[$tmp]) : $text;
    }
    
    /**
     * Translate
     *
     * @param string $text
     * @return string
     */
    public static function t($text)
    {
        $t = self::getInstance();
        /**
         * @var I18n $t
         */
        return $t($text);
    }
    
    /**
     * Alias of translate
     *
     * @param string $text
     * @return string
     */
    public static function _($text)
    {
        /**
         * @var I18n $t
         */
        $t = self::getInstance();
        return $t($text);
    }
}
