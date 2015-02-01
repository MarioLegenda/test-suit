<?php

namespace App\ToolsBundle\Entity;

use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

class InstallEntity extends GenericEntity
{
    /**
     * @Assert\NotBlank(message = "Name has to be provided")
     * @Assert\NotNull(message = "Name has to be provided")
     */
    private $name;

    /**
     * @Assert\NotBlank(message = "Lastname has to be provided")
     * @Assert\NotNull(message = "Lastname has to be provided")
     */
    private $lastname;

    /**
     * @Assert\NotBlank(message = "Username has to be provided")
     * @Assert\NotNull(message = "Username has to be provided")
     * @Assert\Email(message = "Given username (email) is not valid")
     */
    private $username;

    /**
     * @Assert\NotBlank(message = "Password has to be provided")
     * @Assert\NotNull(message = "Password has to be provided")
     * @Assert\Length(min = "8", minMessage = "Password has to have at least 8 characters")
     */
    private $password;

    /**
     * @Assert\NotBlank(message = "Confirmed password has to be provided")
     * @Assert\NotNull(message = "Confirmed has to be provided")
     * @Assert\Length(min = "8")
     */
    private $pass_repeat;

    public function __construct(array $constructValues) {
        $this->setName($constructValues['name']);
        $this->setLastname($constructValues['lastname']);
        $this->setUsername($constructValues['username']);
        $this->setPassword($constructValues['userPassword']);
        $this->setPassRepeat($constructValues['userPassRepeat']);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassRepeat($passRepeat) {
        $this->pass_repeat = $passRepeat;
    }

    public function getPassRepeat() {
        return $this->pass_repeat;
    }

    /**
     * @Assert\Callback
     */
    public function validatePasswordEquality(ExecutionContextInterface $context)
    {
        if(strcmp($this->getPassword(), $this->getPassRepeat()) !== 0) {
            $context->buildViolation('Given user password are not the same')
                ->atPath('password')
                ->addViolation();
        }
    }
} 