<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace App\Cms\Ui;

use Capsule\Component\SectionManager\Section as s;
use Capsule\Component\SectionManager\ToStringExceptionizer;

/**
 * Section.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Section extends s
{
    /**
     * implicit conversion to a string
     *
     * (non-PHPdoc)
     * @see \Capsule\WebUiSection::__toString()
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        /**
         * @var SectionManager $ui
         */
        $ui = SectionManager::getInstance();
        try {
            return $ui($this);
        } catch (\Exception $e) {
            set_error_handler(['\Capsule\Component\SectionManager\ToStringExceptionizer', 'errorHandler']);
            return ToStringExceptionizer::throwException($e);
        }
    }
}