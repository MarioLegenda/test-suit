<?php

namespace App\ToolsBundle\Repositories\Query\Mapper;


interface ObservableInterface
{
    function attach(ObserverInterface $observer);
    function notify();
} 