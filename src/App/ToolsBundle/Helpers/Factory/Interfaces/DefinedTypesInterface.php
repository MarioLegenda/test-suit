<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 18.3.2015.
 * Time: 18:46
 */

namespace App\ToolsBundle\Helpers\Factory\Interfaces;


interface DefinedTypesInterface
{
    function hasAnyType();
    function hasType($type);
    function isValidType($type);
    function getType($type);
} 