<?php
namespace Capsule\Traits;
trait setActive
{
    protected function setActive($value) {
        $this->data['active'] = $value ? 1 : 0;
    }
}