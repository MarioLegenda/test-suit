<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 5.2.2015.
 * Time: 0:16
 */

namespace App\ToolsBundle\Helpers;

use Symfony\Component\HttpFoundation\Response;

class BadAjaxRequest
{
    private $errors = array();

    private static $instance;

    public static function init($message) {
        if( ! self::$instance instanceof self) {
            self::$instance = new BadAjaxRequest($message);
        }

        return self::$instance;
    }

    public function __construct($message) {
        $this->errors['errors'][0] = "User with these credentials already exists.";
    }

    public function getResponse() {
        $response = new Response(json_encode($this->errors));
        $response->setStatusCode(400, "BAD");

        $this->errors = array();
        return $response;
    }
} 