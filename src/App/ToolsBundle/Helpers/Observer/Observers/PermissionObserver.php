<?php

namespace App\ToolsBundle\Helpers\Observer\Observers;


use App\ToolsBundle\Helpers\Observer\Contracts\Observable;
use App\ToolsBundle\Helpers\Observer\Contracts\Observer;

class PermissionObserver implements Observer
{
    private $assignatedTests;

    private $permission = null;

    public function __construct($assignedTests) {
        $this->assignatedTests = $assignedTests;
    }

    public function update(Observable $observable) {
        $this->establishPermission();

        if($this->permission !== 'public' AND $this->permission !== 'restricted') {
            return false;
        }

        $observable->setStatus($this->permission);
    }

    private function establishPermission() {
        if(count($this->assignatedTests) === 1) {
            $at = $this->assignatedTests[0];

            $userId = $at->getUserId();
            $isPublic = $at->getPublicTest();

            if($userId === null AND $isPublic === 1) {
                $this->permission = 'public';
                return true;
            }
        }

        foreach($this->assignatedTests as $at) {
            $userId = $at->getUserId();
            $isPublic = $at->getPublicTest();

            if($userId === null OR $isPublic === 1) {
                return false;
            }
        }

        $this->permission = 'restricted';
    }
} 