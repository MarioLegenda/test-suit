<?php

namespace App\ToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_info")
 */

class UserInfo extends GenericEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    private $user_info_id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fields = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $programming_languages = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tools = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     */
    private $years_of_experience = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $future_plans = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\ToolsBundle\Entity\User", inversedBy="user_info")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     **/
    private $user;

    public function __construct() {

    }


    public function getUserInfoId() {
        return $this->user_info_id;
    }

    public function setUserId($userId) {
        $this->user_id = $userId;

        return $this;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setFields($fields) {
        if( ! empty($fields)) {
            $this->fields = $fields;
        }

        return $this;
    }

    public function getFields() {
        return $this->fields;
    }

    public function setProgrammingLanguages($programmingLanguages) {
        if( ! empty($programmingLanguages)) {
            $this->programming_languages = $programmingLanguages;
        }

        return $this;
    }

    public function getProgrammingLanguages() {
        return $this->programming_languages;
    }

    public function setTools($tools) {
        if( ! empty($tools)) {
            $this->tools = $tools;
        }

        return $this;
    }

    public function getTools() {
        return $this->tools;
    }

    public function setYearsOfExperience($yearsOfExperience) {
        if( ! empty($yearsOfExperience)) {
            $this->years_of_experience = $yearsOfExperience;
        }

        return $this;
    }

    public function getYearsOfExperience() {
        return $this->years_of_experience;
    }

    public function setFuturePlans($fp) {
        if( ! empty($fp)) {
            $this->future_plans = $fp;
        }
    }

    public function getFuturePlans() {
        return $this->future_plans;
    }

    public function setDescription($description) {
        if( ! empty($description)) {
            $this->description = $description;
        }

        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setUser(User $user) {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @Assert\Callback
     */
    public function validatePasswordEquality(ExecutionContextInterface $context)
    {
        $yof = $this->getYearsOfExperience();
        if($yof !== null AND !is_numeric($yof)) {
            $context->buildViolation('If provided, years of experience have to be an number')
                ->atPath('years_of_experience')
                ->addViolation();
        }
    }
}
