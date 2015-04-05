<?php

namespace App\ToolsBundle\Helpers\Observer\Observables;


use App\ToolsBundle\Helpers\Observer\Contracts\Observable;
use App\ToolsBundle\Helpers\Observer\Contracts\Observer;
use App\ToolsBundle\Helpers\Observer\Exceptions\ObserverException;

class PermissionObservable implements Observable
{
    private $observers = array();
    private $status;

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function notify() {
        foreach($this->observers as $observer) {
            if($observer->update($this) === false) {
                throw new ObserverException('PermissionObservable has failed. Could not determine a valid permission');
            }
        }
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
} 