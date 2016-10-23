<?php
namespace Capsule\Traits;
use PHP\Exceptionizer\Exceptionizer;
trait sortOrder
{
    protected function setSortOrder($value, $name) {
        $e = new Exceptionizer;
        settype($value, 'string');
        $this->data[$name] = ctype_digit($value) ? $value : 0;
    }
    
    protected function getSortOrder($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : 0;
    }
}