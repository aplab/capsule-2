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

namespace Capsule\Component\Json\Loader;


class Loader
{
    /**
     * json_decode options
     *
     * @var int
     */
    protected $options = JSON_BIGINT_AS_STRING;

    /**
     * Prefilter accept json string
     *
     * @var \Closure
     */
    protected $prefilter;

    /**
     * Prefilter accept array
     *
     * @var \Closure
     */
    protected $postfilter;

    /**
     * File path
     *
     * @var string
     */
    protected $path;

    /**
     * Loader constructor.
     * @param $path
     * @throws \Exception
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function loadToArray()
    {
        $json = file_get_contents($this->path);
        if (is_callable($this->prefilter)) {
            $json = $this->prefilter($json);
        }
        $data = json_decode($json, true, 512, $this->options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }
        if (is_callable($this->postfilter)) {
            $data = $this->postfilter($data);
        }
        return $data;
    }
}