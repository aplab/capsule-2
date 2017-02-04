<?php

/**
 * FontAwesome Iterator
 *
 * PHP Version 5.3
 *
 * @category  Library
 * @package   bca/fontawesomeiterator
 * @author    Brodkin CyberArts <support@brodkinca.com>
 * @copyright 2013 Brodkin CyberArts.
 * @license   MIT http://opensource.org/licenses/MIT
 * @version   GIT: $Id$
 * @link      https://github.com/brodkinca/BCA-PHP-FontAwesomeIterator
 */

namespace Capsule\Plugin\IconList;

use \ArrayIterator;

/**
 * FontAwesome Iterator
 *
 * Iterate through the icons in FontAwesome or get them as an array.
 *
 * @category  Library
 * @package   bca/fontawesomeiterator
 * @author    Brodkin CyberArts <support@brodkinca.com>
 * @copyright 2013 Brodkin CyberArts.
 * @license   MIT http://opensource.org/licenses/MIT
 * @version   GIT: $Id$
 * @link      https://github.com/brodkinca/BCA-PHP-FontAwesomeIterator
 */
class Iterator extends ArrayIterator
{
    /**
     * FontAwesome CSS Prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Constructor
     *
     * @param string $path Path to FontAwesome CSS
     */
    public function __construct($path, $fa_css_prefix = 'fa')
    {
        $this->prefix = $fa_css_prefix;

        $css = file_get_contents($path);

        $pattern = '/\.('.$fa_css_prefix.'-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';

        preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $icon = new Icon($this, $match[1], $match[2]);
            $this->addIcon($icon);
        }
    }

    /**
     * @param Icon $icon
     */
    private function addIcon(Icon $icon)
    {
        $this->append($icon);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return (string) $this->prefix;
    }
}
