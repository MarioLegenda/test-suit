<?php

namespace App\ToolsBundle\Helpers;

use Symfony\Component\HttpFoundation\Response;

class BadAjaxResponse
{
    private $errors = array();

    private static $instance;

    public static function init($message = null, array $errors = null) {
        if( ! self::$instance instanceof self) {
            self::$instance = new BadAjaxResponse($message, $errors);
        }

        return self::$instance;
    }

    public function __construct($message = null, array $errors = null) {
        if($message === null AND $errors === null) {
            $this->errors['errors'][0] = "Something went wrong. Please, refresh the page and try again";
        }
        else if($message === null AND $errors !== null) {
            $this->errors = $errors;
        }
        else if($message !== null AND $errors === null) {
            $error = array();
            $error[0] = $message;
            $this->errors['errors'] = $error;
        }
    }

    public function getResponse($status = 400) {
        $response = new Response();
        $response->setContent(json_encode($this->errors));
        $response->setStatusCode($status, "BAD");

        $this->errors = array();
        return $response;
    }
} 