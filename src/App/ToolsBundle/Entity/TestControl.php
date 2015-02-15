<?php

namespace App\ToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_control")
 */

class TestControl
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $test_control_id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(message = "Test name has to be provided")
     * @Assert\NotNull(message = "Test name has to be provided")
     */
    private $test_name;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank(message = "If test visibility is not public, then at least one user has to be provided as the one who can solve the test")
     * @Assert\NotNull(message = "If test visibility is not public, then at least one user has to be provided as the one who can solve the test")
     */
    private $visibility;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remarks = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\ToolsBundle\Entity\User", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     **/
    private $user;

    public function __construct() {
        $this->visibility = json_encode(array('public'));
    }

    public function setTestControlId($id) {
        $this->test_control_id = $id;
    }

    public function getTestControlId() {
        return $this->test_control_id;
    }

    public function setUserId($userid) {
        $this->user_id = $userid;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setTestName($testName) {
        $this->test_name = $testName;
    }

    public function getTestName() {
        return $this->test_name;
    }

    public function setVisibility(array $visibility) {
        $this->visibility = json_encode($visibility);
    }

    public function getVisibility() {
        return json_decode($this->visibility);
    }

    public function setRemarks($r) {
        $this->remarks = $r;
    }

    public function getRemarks() {
        return $this->remarks;
    }

    public function setUser(User $u) {
        $this->user = $u;
    }

    public function getUser() {
        return $this->user;
    }

    /**
     * @Assert\Callback
     */

    public function validateVisibility(ExecutionContextInterface $context)
    {
        if( ! is_array($this->getVisibility())) {
            $context->buildViolation('If test visibility is not public, then at least one user has to be provided as the one who can solve the test')
                ->atPath('visibility')
                ->addViolation();

            return;
        }

        $v = $this->getVisibility();
        if(empty($v)) {
            $context->buildViolation('If test visibility is not public, then at least one user has to be provided as the one who can solve the test')
                ->atPath('visibility')
                ->addViolation();

            return;
        }


    }


} 