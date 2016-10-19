<?php
/**
 * This file is part of Aplab Capsule.
 * @copyright 2004-2015 Alexander Polyanin (polyanin@gmail.com)
 *
 * @link      https://github.com/aplab/capsule
 * @license   http://www.aplab.ru/licence
 */


namespace Capsule\Tools\Seo;


use Capsule\Tools\Sysinfo;

class Nofollow
{
    /**
     * @var array
     */
    private $exclude;

    /**
     * @param void
     * @return self
     */
    public function __construct()
    {
        $host = strtolower(Sysinfo::host());
        $this->exclude = array($host => $host);
    }

    /**
     * Adds a host to the exceptions.
     * The name must be without protocol
     * @example www.aplab.ru
     *
     * @param string $host
     * @return void
     */
    public function exclude($host)
    {
        $host = strtolower($host);
        $this->exclude[$host] = $host;
    }

    public function __invoke($html)
    {
        $exclude = array_filter($this->exclude);
        array_walk($exclude, function (& $v, $k) {
            $v = '[^\\/]*?' . preg_quote($v, '/');
        });
        $exclude = join('|', $exclude);
        return preg_replace_callback('/<a\\s+[^>]+>/isu', function ($m) use ($exclude) {
            if (preg_match('/href\\s*=\\s*"([^"]*)"/isu', $m[0], $href) ||
                preg_match('/href\\s*=\\s*\'([^\']*)\'/isu', $m[0], $href) ||
                preg_match('/href\\s*=\\s*(\\S+)/isu', $m[0], $href)
            ) {
                if (preg_match('/(http:|https:|)\\/\\/(?!' . $exclude . ')/isu', $href[1], $external)) {
                    if (preg_match('/rel\\s*=\\s*/', $m[0])) {
                        // здесь мы просто заменяем, не учитывая имеющееся значение атрибута rel
                        $m[0] = preg_replace('/rel\\s*=\\s*"([^"]*)"/isu', '', $m[0]);
                        $m[0] = preg_replace('/rel\\s*=\\s*\'([^\']*)\'/isu', '', $m[0]);
                        $m[0] = preg_replace('/rel\\s*=\\s*(\\S+)/isu', '', $m[0]);
                    }
                    $m[0] = preg_replace('/^<a/', '<a rel="nofollow"', preg_replace('/\\s+/', ' ', $m[0]));
                }
            }
            return $m[0];
        }, $html);
    }
}