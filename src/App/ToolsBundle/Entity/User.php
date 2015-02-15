<?php

namespace App\ToolsBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */

class User extends GenericEntity implements UserInterface, \Serializable, \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "Username has to be provided")
     * @Assert\NotNull(message = "Username has to be provided")
     * @Assert\Email(message = "Username has to be a valid email")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 4096)
     * @Assert\NotBlank(message = "Password has to be provided")
     * @Assert\NotNull(message = "Password has to be provided")
     * @Assert\Length(
     *      min = 8,
     *      max = 4096,
     *      minMessage = "Password has to be at least 8 characters long",
     * )
     */
    private $password;

     /**
     * @Assert\Length(max = 4096)
     * @Assert\NotBlank(message = "Confirmed password has to be provided")
     * @Assert\NotNull(message = "Confirmed password has to be provided")
     * @Assert\Length(
     *      min = 8,
     *      max = 4096,
     *      minMessage = "Password has to be at least 8 characters long",
     * )
     */
    private $passRepeat;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "Name has to be provided")
     * @Assert\NotNull(message = "Name has to be provided")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "Lastname has to be provided")
     * @Assert\NotNull(message = "Lastname has to be provided")
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $logged;

    /**
     * @ORM\OneToMany(targetEntity="App\ToolsBundle\Entity\Role", mappedBy="user", cascade="persist")
     **/
    private $roles;


    /**
     * @ORM\OneToOne(targetEntity="App\ToolsBundle\Entity\UserInfo", mappedBy="user", cascade="persist")
     **/
    private $user_info;

    public function __construct() {
        $this->logged = new \DateTime();
        $this->roles = new ArrayCollection();
    }

    public function setUserId($id) {
        $this->user_id = $id;
    }

    public function getUserId() {
        return $this->user_id;
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

    public function setPassRepeat($pass) {
        $this->passRepeat = $pass;
    }

    public function getPassRepeat() {
        return $this->passRepeat;
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

    public function setLogged(\DateTime $date) {
        $this->logged = $date;
    }

    public function getLogged() {
        return $this->logged;
    }

    public function setUserInfo(UserInfo $userInfo) {
        $this->user_info = $userInfo;
    }

    public function getUserInfo() {
        return $this->user_info;
    }



    public function setRoles(Role $role) {
        $this->roles->add($role);
    }

    public function getRoles() {
        return $this->roles->toArray();
    }

    public function isInRole($roleType) {
        $role = $this->getRoles()[0]->getRole();

        return $roleType === $role;
    }

    public function getSalt() {
        return '8sfd4g68ds4fg98d48mk81';
    }

    public function eraseCredentials() {

    }

    public function serialize()
    {
        return serialize(array(
            $this->user_id,
            $this->username,
            $this->password,
            $this->name,
            $this->lastname,
            $this->logged
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->user_id,
            $this->username,
            $this->password,
            $this->name,
            $this->lastname,
            $this->logged
            ) = unserialize($serialized);
    }

    /**
     * @Assert\Callback
     */
    public function validatePasswordEquality(ExecutionContextInterface $context)
    {
        if(strcmp($this->getPassword(), $this->getPassRepeat()) !== 0) {
            $context->buildViolation('Given passwords have to be equal')
                ->atPath('password')
                ->addViolation();
        }
    }

    public function jsonSerialize() {
        return array(
            'name' => $this->getName(),
            'lastname' => $this->getLastname(),
            'username' => $this->getUsername()
        );
    }
} 