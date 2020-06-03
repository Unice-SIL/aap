<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AclRepository")
 * @UniqueEntity(fields={"user", "permission", "common"})
 * @UniqueEntity(fields={"groupe", "permission", "common"})
 * @Table(
 *     uniqueConstraints={
 *          @UniqueConstraint(
 *              name="uc_user_permission",
 *              columns= {"user_id", "permission", "common_id"}
 *          ),
 *          @UniqueConstraint(
 *              name="uc_groupe_permission",
 *              columns= {"groupe_id", "permission", "common_id"}
 *          )
 *      }
 * )
 */

class Acl
{
    const PERMISSION_ADMIN = 'admin';
    const PERMISSION_MANAGER = 'manager';
    const PERMISSION_VIEWER = 'viewer';

    const EDITOR_PERMISSIONS = [
        self::PERMISSION_ADMIN,
        self::PERMISSION_MANAGER,
    ];

    const PERMISSION_BASES = [
      self::PERMISSION_ADMIN,
      self::PERMISSION_MANAGER,
      self::PERMISSION_VIEWER
    ];

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="acls")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Group")
     */
    private $groupe;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Choice(choices=Acl::PERMISSION_BASES)
     */
    private $permission;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Common", inversedBy="acls")
     */
    private $common;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGroupe(): ?Group
    {
        return $this->groupe;
    }

    public function setGroupe(?Group $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function getCommon(): ?Common
    {
        return $this->common;
    }

    public function setCommon(?Common $common): self
    {
        $this->common = $common;

        return $this;
    }

    public function __toString()
    {
        return $this->getUser() ? '(Utilisateur) ' . $this->getUser()->getUsername() : '(Groupe) ' . $this->getGroupe()->getName();
    }

    public function getName()
    {
        return $this->getUser() ? '(Utilisateur) ' . $this->getUser()->getUsername() : '(Groupe) ' . $this->getGroupe()->getName();
    }

    public function getEntity()
    {
        return $this->getUser() ?? $this->getGroupe();
    }
}
