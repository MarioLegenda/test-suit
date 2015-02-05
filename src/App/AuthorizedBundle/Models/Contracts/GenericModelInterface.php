<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 2.2.2015.
 * Time: 22:04
 */

namespace App\AuthorizedBundle\Models\Contracts;


interface GenericModelInterface
{
    function isInRole($roleType);
} 