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

    public function isValidPagination() {
        if( ! array_key_exists('start', $this->content) AND ! array_key_exists('end', $this->content)) {
            return false;
        }

        return true;
    }

    public function isUserInfoValid() {
        if( ! array_key_exists('id', $this->content) OR empty($this->content['id'])) {
            return false;
        }

        return true;
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

        if($this->filterType === 'username-filter') {
            if($this->key !== 'username') {
                return false;
            }

            return true;
        }
        else if($this->filterType === 'personal-filter') {
            if($this->key !== 'personal') {
                return false;
            }

            $content = $this->content[$this->key];

            if( ! array_key_exists('name', $content)) {
                return false;
            }

            if( ! array_key_exists('lastname', $content)) {
                return false;
            }
        }

        return true;
    }

    public function getType() {
        return $this->filterType;
    }

    public function getPureContent() {
        return $this->content[$this->key];
    }

} 