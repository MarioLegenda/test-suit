<?php

namespace App\ToolsBundle\Repositories\Query\Mapper;

class Mapper implements ObservableInterface
{
    private $observers = array();
    private $mapped = array();

    public function attach(ObserverInterface $observer) {
        $this->observers[] = $observer;
    }

    public function notify() {
        foreach($this->observers as $obs) {
            $this->mapped[] = $obs->update($this);
        }
    }

    public function offsetExists($index) {
        return array_key_exists($index, $this->mapped);
    }

    public function offsetGetMapped($index) {
        if( ! $this->offsetExists($index)) {
            return null;
        }

        return $this->mapped[$index];
    }
} 