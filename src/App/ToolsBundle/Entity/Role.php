<?php

namespace App\ToolsBundle\Entity;

use App\ToolsBundle\Helpers\Contracts\ModelObjectWrapperInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */

class Role implements RoleInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $role_id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="App\ToolsBundle\Entity\User", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     **/
    private $user;

    public function setRoleId($id) {
        $this->role_id = $id;
    }

    public function getRoleId() {
        return $this->role_id;
    }

    public function setUser(User $admin) {
        $this->user = $admin;
    }

    public function setUserId($id) {
        $this->user_id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getRole() {
        return $this->role;
    }
}