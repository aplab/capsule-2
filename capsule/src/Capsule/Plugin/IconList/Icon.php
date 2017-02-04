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

/**
 * FontAwesome Icon
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
/**
 * Class Icon
 * @package Capsule\Plugin\IconList
 */
class Icon
{
    /**
     * Associative Array of Icon Data
     *
     * @var array
     */
    private $data = array();

    /**
     * Iterator
     *
     * @var Iterator
     */
    private $iterator;

    /**
     * Constructor
     *
     * @param string $class   Icon css class
     * @param string $unicode Unicode character reference
     */
    public function __construct(Iterator $iterator, $class, $unicode)
    {
        $this->iterator = $iterator;

        // Set Basic Data
        $this->data['class'] = $class;
        $this->data['unicode'] = $unicode;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function __get($key)
    {
        if (strtolower($key) === 'name') {
            return $this->getName($this->__get('class'));
        }

        return @$this->data[$key];
    }

    /**
     * @param $class
     * @return mixed|string
     */
    private function getName($class)
    {
        // Remove Prefix
        $name = substr($class, strlen($this->iterator->getPrefix()) + 1);

        // Convert Hyphens to Spaces
        $name = str_replace('-', ' ', $name);

        // Show Directional Variants in Parenthesis
        $directions = array('/up$/i', '/down$/i', '/left$/i', '/right$/i');
        $directionsFormat = array('(Up)', '(Down)', '(Left)', '(Right)');
        $name = preg_replace($directions, $directionsFormat, $name);

        // Use Word "Outlined" in Place of "O"
        $outlinedVariants = array('/\so$/i', '/\so\s/i');
        $name = preg_replace($outlinedVariants, ' Outlined ', $name);

        // Remove Trailing Characters
        $name = trim($name);

        return $name;
    }
}
