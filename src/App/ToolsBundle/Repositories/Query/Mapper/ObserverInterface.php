<?php

namespace App\ToolsBundle\Repositories\Query\Mapper;


interface ObserverInterface
{
    function update(ObservableInterface $observable);
} 