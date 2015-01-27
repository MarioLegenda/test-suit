<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 26.1.2015.
 * Time: 12:31
 */

namespace App\PublicBundle\Models\Contracts;


interface DependencyManagerInterface
{
    function getDependency($key);
} 