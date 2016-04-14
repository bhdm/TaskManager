<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fos_role")
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="title")
     */
    protected $name;


    /**
     * @var Role[]
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Role", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @var ArrayCollection|Role[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Role", mappedBy="parent")
     */
    private $children;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="roles")
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->children->count()) {
            $childNameList = array();
            foreach ($this->children as $child) {
                $childNameList[] = $child->getName();
            }
            return ''.sprintf('%s [%s]', $this->name, implode(', ', $childNameList));
        }
        return ''.sprintf('%s', $this->name);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return Role[]
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Role $parent
     * @param $addChildToParent boolean
     */
    public function setParent(Role $parent, $addChildToParent = true)
    {
        $addChildToParent && $parent->addChildren($this, false);
        $this->parent = $parent;
    }

    /**
     * @return Role[]|ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Role[]|ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function addUser($user, $addRoleToUser = true)
    {
        $this->users->add($user);
        $addRoleToUser && $user->addRole($this, false);
    }

    public function removeUser($user)
    {
        $this->users->removeElement($user);
    }

    public function addChildren(Role $child, $setParentToChild = true)
    {
        $this->children->add($child);
        $setParentToChild && $child->setParent($this, false);
    }

    public function getDescendant(& $descendants = array())
    {
        foreach ($this->children as $role) {
            $descendants[spl_object_hash($role)] = $role;
            $role->getDescendant($descendants);
        }
        return $descendants;
    }

    public function removeChildren(Role $children)
    {
        $this->children->removeElement($children);
    }

}