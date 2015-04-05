<?php

namespace App\ToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="assigned_tests")
 */

class AssignedTests
{
    /**
     * @ORM\Column(type="integer", name="assigned_tests_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $assignedTestsId;

    /**
     * @ORM\Column(type="integer", nullable=true, name="user_id")
     */
    private $userId;

    /**
     * @ORM\Column(type="integer", nullable=false, name="test_control_id")
     */
    private $testControlId;

    /**
     * @ORM\Column(type="integer", nullable=false, name="public_test")
     */
    private $publicTest;

    public function setAssignedTestsId($id) {
        $this->assignedTestsId = $id;
    }

    public function getAssignedTestsId() {
        return $this->assignedTestsId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setTestControlId($testControlId) {
        $this->testControlId = $testControlId;
    }

    public function getTestControlId() {
        return $this->testControlId;
    }

    public function setPublicTest($public) {
        $this->publicTest = $public;
    }

    public function getPublicTest() {
        return $this->publicTest;
    }
} 