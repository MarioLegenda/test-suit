<?php

namespace App\ToolsBundle\Tests\ObserverPattern;


use App\ToolsBundle\Entity\AssignedTests;
use App\ToolsBundle\Helpers\Observer\Observables\PermissionObservable;
use App\ToolsBundle\Helpers\Observer\Observers\PermissionObserver;

class ObserverPatternTest extends \PHPUnit_Framework_TestCase
{
    public function testPublicPermissionObserver() {
        $at = new AssignedTests();
        $at->setTestControlId(5);
        $at->setPublicTest(1);
        $at->setUserId(null);

        $content = array($at);

        $observable = new PermissionObservable();
        $observable->attach(new PermissionObserver($content));
        $observable->notify();

        $this->assertEquals('public', $observable->getStatus(),
            'ObserverPatternTest::testPublicPermissionObserver-> PermissionObservable::getStatus had to return \'public\' but returned ' . $observable->getStatus());
    }

    public function testRestrictedPermissionObserver() {
        $content = array();
        for($i = 0; $i < 5; $i++) {
            $at = new AssignedTests();
            $at->setTestControlId($i);
            $at->setPublicTest(0);
            $at->setUserId(3);

            $content[] = $at;
        }

        $observable = new PermissionObservable();
        $observable->attach(new PermissionObserver($content));
        $observable->notify();

        $this->assertEquals('restricted', $observable->getStatus(),
            'ObserverPatternTest::testRestrictedPermissionObserver-> PermissionObservable::getStatus had to return \'public\' but returned ' . $observable->getStatus());
    }
} 