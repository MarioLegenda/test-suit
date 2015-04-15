<?php

namespace Tests;


use Demo\User;
use EntityToArray\EntityToArray;

class EntityToArrayTest extends \PHPUnit_Framework_TestCase
{
    private $users = array();

    public function __construct() {
        $userArray[] = array(
            'name' => 'Mario',
            'lastname' => 'Škrlec',
            'age' => 23,
            'logged' => new \DateTime()
        );

        $userArray[] = array(
            'name' => 'Ivana',
            'lastname' => 'Preljvukić',
            'age' => 28,
            'logged' => new \DateTime()
        );

        $userArray[] = array(
            'name' => 'Marijeta',
            'lastname' => 'Doktonić',
            'age' => 22,
            'logged' => new \DateTime()
        );

        $userArray[] = array(
            'name' => 'Martina',
            'lastname' => 'Mistinić',
            'age' => 28,
            'logged' => new \DateTime()
        );

        $users = array();
        foreach($userArray as $userEntry) {
            $user = new User();
            $user->setName($userEntry['name']);
            $user->setLastname($userEntry['lastname']);
            $user->setAge($userEntry['age']);
            $user->setLogged($userEntry['logged']);

            $users[] = $user;
        }

        $this->users = $users;
    }

    public function testConstruction() {
        foreach($this->users as $user) {
            $this->assertInstanceOf('Demo\\User', $user,
                'EntityToArrayTest::testConstruction()-> User array entry is not of type user');
        }
    }

    public function testSingleMethod() {
        $eta = new EntityToArray($this->users, array('getLastname'));

        $namesAsArray = $eta->config(array(
            'numeric-keys' => true,
            'multidimensional' => false
        ))->toArray();

        foreach($namesAsArray as $name) {
            $this->assertInternalType('string', $name,
                'EntityToArrayTest::testSingleMethod()-> EntityToArray::toArray() should return a single dimensional array with user names');
        }
    }

    public function testSingleMethodWithMethodNameKey() {
        $eta = new EntityToArray($this->users, array('getLastname'));

        $namesAsArray = $eta->config(array(
            'methodName-keys' => true,
            'multidimensional' => false
        ))->toArray();

        foreach($namesAsArray as $methodNameKey => $value) {
            $this->assertInternalType('array', $value,
                'EntityToArrayTest::testSingleMethodWithMethodNameKey()-> EntityToArray::toArray() should return a multidimensional array with method name keys');

            $this->assertArrayHasKey('getLastname', $value,
                'EntityToArrayTest::testSingleMethodWithMethodNameKey()-> EntityToArray::toArray() should have a \'getLastname\' key');
        }
    }

    public function testMultipleMethods() {
        $eta = new EntityToArray($this->users, array(
            'getName',
            'getLastname',
            'getAge',
            'getLogged'
        ));

        $userAsArray = $eta->config(array(
            'numeric-keys' => true
        ))->toArray();

        foreach($userAsArray as $key => $value) {
            $this->assertInternalType('array', $value,
                'EntityToArrayTest::testSingleMethodWithMethodNameKey()-> EntityToArray::toArray() should return array');

            $this->assertInternalType('integer', $key,
                'EntityToArrayTest::testSingleMethodWithMethodNameKey()-> EntityToArray::toArray()  should be an integer value');
        }
    }

    public function testMultipleMethodsWithMethodNameKeys() {
        $eta = new EntityToArray($this->users, array(
            'getName',
            'getLastname',
            'getAge',
            'getLogged'
        ));

        $userAsArray = $eta->config(array(
            'methodName-keys' => true,
            'multidimensional' => false,
            'only-names' => true
        ))->toArray();

        foreach($userAsArray as $key => $value) {
            $this->assertInternalType('array', $value,
                'EntityToArrayTest::testMultipleMethodsWithMethodNameKey()-> EntityToArray::toArray() should return array');
        }
    }


} 