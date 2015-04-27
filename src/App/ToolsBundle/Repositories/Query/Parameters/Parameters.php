<?php

namespace App\ToolsBundle\Repositories\Query\Parameters;

class Parameters implements \Iterator, \Countable
{
    private $parameters = array();

    private $index = 0;

    public function attach($param, $value, $dataType) {
        $this->parameters[] = new Parameter($param, $value, $dataType);
    }

    public function initialize(\Closure $closure) {
        $closure->__invoke($this);
    }

    public function current() {
        return $this->parameters[$this->index];
    }

    public function key() {
        return $this->index;
    }

    public function next() {
        $this->index += 1;
    }

    public function rewind() {
        $this->index = 0;
    }

    public function valid() {
        return array_key_exists($this->index, $this->parameters);
    }

    public function count() {
        return count($this->parameters);
    }

    public function clear() {
        $this->parameters = array();
        $this->index = 0;

        return $this;
    }

    public function areEmpty() {
        return ($this->count() === 0) ? true : false;
    }
}