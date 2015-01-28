<?php

namespace App\PublicBundle\Helpers;

use Symfony\Component\HttpFoundation\Response;

class GenericAjaxResponseWrapper
{
    private $response;

    public function __construct($status, $message, ResponseParameters $content) {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setProtocolVersion('1.1');
        $response->setStatusCode($status, $message);

        $response->setContent(json_encode($content));

        $this->response = $response;
    }

    public function getResponse() {
        return $this->response;
    }
} 