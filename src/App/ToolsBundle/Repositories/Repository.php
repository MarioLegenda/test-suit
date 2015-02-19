<?php

namespace App\ToolsBundle\Repositories;


abstract class Repository
{
    protected $doctrine;
    protected $em;
    protected $security;

    public function __construct($doctrine, $security = null) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();

        if($security !== null) {
            $this->security = $security;
        }
    }
} 