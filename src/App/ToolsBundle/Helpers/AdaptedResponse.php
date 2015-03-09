<?php

namespace App\ToolsBundle\Helpers;

use Symfony\Component\HttpFoundation\Response;

class AdaptedResponse
{
    private $content = null;

    public function setContent(ResponseParameters $content) {
        $this->content = $content;
    }

    public function sendResponse($status = 400, $message = "BAD ") {
        if($this->content === null) {
            throw new \Exception("AdaptedResponse: Content is null");
        }

        $response = new Response();
        $response->setContent(json_encode($this->content));
        $response->setStatusCode($status, $message);

        $this->content = null;
        return $response;
    }


} 