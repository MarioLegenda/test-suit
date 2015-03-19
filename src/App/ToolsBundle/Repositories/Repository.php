<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Helpers\AppLogger;
use App\ToolsBundle\Helpers\Contracts\LoggerInterface;
use App\ToolsBundle\Helpers\Factory\ParameterInterface;
use App\ToolsBundle\Helpers\Factory\Parameters;

abstract class Repository implements LoggerInterface, ParameterInterface
{
    protected $doctrine;
    protected $em;
    protected $security;

    protected $logger;

    public function __construct(Parameters $parameters) {
        $this->doctrine = $parameters->getParameter('doctrine');
        $this->em = $this->doctrine->getManager();

        if($parameters->getParameter('security') !== null) {
            $this->security = $parameters->getParameter('security');
        }
    }

    public function setLogger(AppLogger $logger) {
        $this->logger;
    }
} 