<?php

namespace App\PublicBundle\Models;

use Doctrine\ORM\EntityManager;

abstract class GenericModel
{
    abstract function runModel();
} 