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
 * @Table(
 *     uniqueConstraints={
 *          @UniqueConstraint(
 *              name="uc_user_permission",
 *              columns= {"user_id", "permission", "common_id"}
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
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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
}
