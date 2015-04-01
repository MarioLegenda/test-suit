<?php

namespace App\ToolsBundle\Helpers\Command\Filters;


class Exists implements FilterInterface
{
    private $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function isValid(array $content) {
        if( ! array_key_exists($this->key, $content)) {
            return false;
        }

        return true;
    }
} 