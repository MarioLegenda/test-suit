<?php

namespace App\ToolsBundle\Tests\MiscObjects;

use App\ToolsBundle\Entity\AssignedTests;
use App\ToolsBundle\Helpers\EntityToArray;

class EntityToArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityToArray() {
        $content = array();
        for($i = 0; $i < 5; $i++) {
            $at = new AssignedTests();
            $at->setTestControlId($i);
            $at->setPublicTest(0);
            $at->setUserId($i + 2);

            $content[] = $at;
        }

        $eta = new EntityToArray($content, array('getUserId'));
        $entityArr = $eta
            ->config(array(
                'numeric-keys' => true,
                'use-temp' => false
            ))
            ->toArray();

        var_dump($entityArr);
    }
} 