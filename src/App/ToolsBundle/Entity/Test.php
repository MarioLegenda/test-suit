<?php

namespace App\ToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
 */

class Test
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $test_id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $test_serialized;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $isFinished;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    public function __construct() {

    }

    public function setTestId($id) {
        $this->test_id = $id;
    }

    public function getTestId() {
        return $this->test_id;
    }

    public function setTestSerialized($test) {
        $this->test_serialized = $test;
    }

    public function getTestSerialized() {
        return $this->test_serialized;
    }

    public function setIsFinished($isFinished) {
        $this->isFinished = $isFinished;
    }

    public function getIsFinished() {
        return $this->isFinished;
    }

    public function setCreated(\DateTime $datetime) {
        $this->created = $datetime;
    }

    public function getCreated() {
        return $this->created;
    }
} 