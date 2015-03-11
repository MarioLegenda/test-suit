<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Helpers\AppLogger;
use App\ToolsBundle\Helpers\Contracts\LoggerInterface;

abstract class Repository implements LoggerInterface
{
    protected $doctrine;
    protected $em;
    protected $security;

    protected $logger;

    public function __construct($doctrine, $security = null) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();

        if($security !== null) {
            $this->security = $security;
        }
    }

    public function setLogger(AppLogger $logger) {
        $this->logger;
    }
} 