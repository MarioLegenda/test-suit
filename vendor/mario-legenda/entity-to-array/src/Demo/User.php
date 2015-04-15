<?php

namespace Demo;


class User
{
    private $name;
    private $lastname;
    private $age;
    private $logged;

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function getAge() {
        return $this->age;
    }

    public function setLogged(\DateTime $logged) {
        $this->logged = $logged;
    }

    public function getLogged() {
        return $this->logged;
    }
} 