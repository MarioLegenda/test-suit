<?php

namespace App\ToolsBundle\Helpers;

use Symfony\Component\HttpFoundation\Response;

class GoodAjaxRequest
{
    private $responseParameters;

    private static $instance;

    public static function init(ResponseParameters $params) {
        if( ! self::$instance instanceof self) {
            self::$instance = new GoodAjaxRequest($params);
        }

        return self::$instance;
    }

    private function __construct(ResponseParameters $params) {
        $this->responseParameters = $params;
    }

    public function getResponse() {
        $response = new Response(json_encode($this->responseParameters->getParameters()));
        $response->setStatusCode(200, "OK");

        $this->responseParameters = null;
        return $response;
    }
} 