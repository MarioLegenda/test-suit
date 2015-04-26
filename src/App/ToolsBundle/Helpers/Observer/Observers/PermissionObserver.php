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

        if($this->permission === 'public' OR $this->permission === 'restricted') {
            $observable->setStatus($this->permission);
            return true;
        }

        return false;
    }

    private function establishPermission() {
        if(count($this->assignatedTests) === 1) {
            $at = $this->assignatedTests[0];

            $userId = (int)$at['user_id'];
            $isPublic = (int)$at['public_test'];

            if(($userId === null OR $userId === 0) AND $isPublic === 1) {
                $this->permission = 'public';
                return true;
            }
        }

        foreach($this->assignatedTests as $at) {
            $userId = (int)$at['user_id'];
            $isPublic = (int)$at['public_test'];

            if($userId === null OR $isPublic === 1) {
                return false;
            }
        }

        $this->permission = 'restricted';
    }
} 