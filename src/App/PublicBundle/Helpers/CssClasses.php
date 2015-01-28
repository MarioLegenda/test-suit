<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.1.2015.
 * Time: 15:30
 */

namespace App\PublicBundle\Helpers;


class CssClasses
{
    private $classes = array();

    public function __construct(array $classes = null) {
        if($classes !== null AND is_array($classes)) {
            $this->classes = $classes;
        }
    }

    public function addClass($key, $class) {
        $this->classes[$key] = $class;
    }

    public function getClass($key) {
        if( ! array_key_exists($key, $this->classes)) {
            return "";
        }

        return $this->classes[$key];
    }
} 