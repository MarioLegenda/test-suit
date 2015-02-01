<?php

namespace App\ToolsBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use App\PublicBundle\Helpers\Contracts\ModelObjectWrapperInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="administrators")
 */

class Administrator extends GenericEntity implements UserInterface, \Serializable, ModelObjectWrapperInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $admin_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */

    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 4096)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $logged;

    /**
     * @ORM\OneToMany(targetEntity="App\ToolsBundle\Entity\Role", mappedBy="administrator", cascade="persist")
     **/
    private $roles;

    public function __construct() {
        $this->logged = new \DateTime();
        $this->roles = new ArrayCollection();
    }

    public function setAdminId($id) {
        $this->admin_id = $id;
    }

    public function getAdminId() {
        return $this->admin_id;
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



    public function setRoles(Role $role) {
        $this->roles->add($role);
    }

    public function getRoles() {
        return $this->roles->toArray();
    }

    public function getSalt() {
        return '8sfd4g68ds4fg98d48mk81';
    }

    public function eraseCredentials() {

    }

    public function serialize()
    {
        return serialize(array(
            $this->admin_id,
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
            $this->admin_id,
            $this->username,
            $this->password,
            $this->name,
            $this->lastname,
            $this->logged
            ) = unserialize($serialized);
    }
} 