<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Helpers\Factory\ParameterInterface;
use App\ToolsBundle\Helpers\Factory\Parameters;

abstract class Repository implements ParameterInterface
{
    protected $doctrine;
    protected $em;
    protected $security;

    public function __construct(Parameters $parameters) {
        $this->doctrine = $parameters->getParameter('doctrine');
        $this->em = $this->doctrine->getManager();

        if($parameters->getParameter('security') !== null) {
            $this->security = $parameters->getParameter('security');
        }
    }
} 