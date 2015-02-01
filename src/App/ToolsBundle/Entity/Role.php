<?php

namespace App\ToolsBundle\Entity;

use App\PublicBundle\Helpers\Contracts\ModelObjectWrapperInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */

class Role implements RoleInterface, ModelObjectWrapperInterface
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
    private $admin_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="App\ToolsBundle\Entity\Administrator", inversedBy="roles")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="admin_id")
     **/
    private $administrator;

    public function setRoleId($id) {
        $this->role_id = $id;
    }

    public function getRoleId() {
        return $this->role_id;
    }

    public function setAdministrator(Administrator $admin) {
        $this->administrator = $admin;
    }

    public function setAdminId($id) {
        $this->admin_id = $id;
    }

    public function getAdminId() {
        return $this->admin_id;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getRole() {
        return $this->role;
    }
}