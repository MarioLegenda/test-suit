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
     * @ORM\Column(type="integer")
     */
    private $test_control_id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $test_serialized;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\ToolsBundle\Entity\TestControl", cascade="persist")
     * @ORM\JoinColumn(name="test_control_id", referencedColumnName="test_control_id")
     **/
    private $test_control;

    public function __construct() {

    }

    public function setTestId($id) {
        $this->test_id = $id;
    }

    public function getTestId() {
        return $this->test_id;
    }

    public function setTestSerialized($test) {
        $this->test_serialized = json_encode($test);
    }

    public function getTestSerialized() {
        return $this->test_serialized;
    }

    public function setCreated(\DateTime $datetime) {
        $this->created = $datetime;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setTestControl(TestControl $testControl) {
        $this->test_control = $testControl;
    }

    public function getTestControl() {
        return $this->test_control;
    }

    public function setTestControlId($id) {
        $this->test_control_id = $id;
    }

    public function getTestControlId() {
        return $this->test_control_id;
    }
} 