<?php

namespace App\ToolsBundle\Helpers\Observer\Contracts;

interface Observable
{
    function attach(Observer $observable);
    function notify();
} 