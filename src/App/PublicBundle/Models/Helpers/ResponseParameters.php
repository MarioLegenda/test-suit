<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.1.2015.
 * Time: 15:30
 */

namespace App\PublicBundle\Models\Helpers;


class ResponseParameters implements \JsonSerializable
{
    private $responseParameters = array();

    public function addParameter($key, $value) {
        if( $value instanceof \Closure ) {
            $parameters = $value->__invoke();
            $this->responseParameters[$key] = $parameters;
        } else if( ! array_key_exists($key, $this->responseParameters) ) {
            $this->responseParameters[$key] = $value;
        }

    }

    public function removeParameter($key) {
        if( ! array_key_exists($key, $this->responseParameters) ) {
            return false;
        }

        unset($this->responseParameters[$key]);
        sort($this->responseParameters);

        return true;
    }

    public function getParameters() {
        return $this->responseParameters;
    }

    public function jsonSerialize() {
        return json_encode($this->getParameters());
    }
} 