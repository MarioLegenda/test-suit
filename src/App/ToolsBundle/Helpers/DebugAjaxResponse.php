<?php

namespace App\ToolsBundle\Helpers;


class DebugAjaxResponse
{
    private $errors = array();

    private static $instance;

    public static function init($message, array $errors = null) {
        if( ! self::$instance instanceof self) {
            self::$instance = new DebugAjaxResponse($message, $errors);
        }

        return self::$instance;
    }

    public function __construct($message, array $errors = null) {
        if($message === null AND $errors === null) {
            $this->errors['errors'][0] = "Something went wrong. Please, refresh the page and try again";
        }
        else if($message === null AND $errors !== null) {
            $this->errors = $errors;
        }
        else if($message !== null AND $errors === null) {
            $this->errors['errors'][0] = $message;
        }
    }

    public function getResponse() {
        $response = new Response(json_encode($this->errors));
        $response->setStatusCode(400, "BAD");

        $this->errors = array();
        return $response;
    }
} 