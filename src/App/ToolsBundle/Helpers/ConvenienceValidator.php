<?php

namespace App\ToolsBundle\Helpers;

class ConvenienceValidator
{
    public static $instance;

    private $errors = null;

    public static function init(array $toValidate, $validator) {
        if( ! self::$instance instanceof self) {
            self::$instance = new ConvenienceValidator($toValidate, $validator);
        }

        return self::$instance;
    }

    private function __construct(array $toValidate, $validator) {
        foreach($toValidate as $objToValidate) {
            $constraintVioliationList = $validator->validate($objToValidate);

            if(count($constraintVioliationList) > 0) {
                $this->errors = array();
                for($i = 0; $i < count($constraintVioliationList); $i++) {
                    $this->errors["errors"][] = $constraintVioliationList->get($i)->getMessage();
                }
            }
        }
    }

    public function getErrors() {
        return $this->errors;
    }
} 