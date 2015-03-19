<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 16.3.2015.
 * Time: 20:33
 */

namespace App\AuthorizedBundle\Models;


use App\ToolsBundle\Helpers\Exceptions\ModelException;
use App\ToolsBundle\Helpers\Factory\ParameterInterface;

class UserModel implements ParameterInterface
{
    private $content = array();
    private $filterType = null;
    private $key = null;

    public function __construct() {

    }

    public function requestContentMode($content) {
        $this->content = $content;

        return $this;
    }

    public function extractType() {
        if(array_key_exists('filterType', $this->content)) {
            $this->filterType = $this->content['filterType'];
        }

        if(array_key_exists('key', $this->content)) {
            $this->key = $this->content['key'];
        }

        return $this;
    }

    public function isContentValid() {
        if($this->filterType === null OR $this->key === null) {
            return false;
        }

        if( ! is_array($this->content)) {
            return false;
        }

        if(empty($this->content)) {
            return false;
        }

        if($this->filterType === 'personal-filter') {
            $content = $this->content[$this->key];
            return ! array_key_exists('name', $content) AND ! array_key_exists('lastname', $content);
        }

        return false;
    }

    public function getType() {
        return $this->filterType;
    }

    public function getPureContent() {
        return $this->content[$this->key];
    }

} 