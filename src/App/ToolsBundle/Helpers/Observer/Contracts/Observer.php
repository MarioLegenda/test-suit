<?php

namespace App\ToolsBundle\Helpers\Observer\Contracts;


interface Observer
{
    function update(Observable $observable);
} 