<?php


namespace App\Entity;

use App\Traits\BlameableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\JoinTable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *     "common" = "Common",
 *      "project" = "Project",
 *      "organizing_center" = "OrganizingCenter",
 *     "call_of_project" = "CallOfProject",
 *     "report" = "Report",
 *     "group" = "Group",
 *     "invitation" = "Invitation",
 *     "dictionary" = "Dictionary",
 * })
 */
class Common
{
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=30)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acl", mappedBy="common", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $acls;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="subscriptions")
     */
    private $subscribers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number;

    /**
     * Common constructor.
     */
    public function __construct()
    {
        $this->acls = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return CallOfProject
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Acl[]
     */
    public function getAcls(): Collection
    {
        return $this->acls;
    }

    /**
     * @param Acl $acl
     * @return Common
     */
    public function addAcl(Acl $acl): self
    {
        if (!$this->acls->contains($acl)) {
            $this->acls[] = $acl;
            $acl->setCommon($this);
        }

        return $this;
    }

    /**
     * @param Acl $acl
     * @return Common
     */
    public function removeAcl(Acl $acl): self
    {
        if ($this->acls->contains($acl)) {
            $this->acls->removeElement($acl);
            // set the owning side to null (unless already changed)
            if ($acl->getCommon() === $this) {
                $acl->setCommon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    /**
     * @param Collection $subscribers
     * @return Common
     */
    public function setSubscribers(Collection $subscribers): Common
    {
        $this->subscribers = $subscribers;
        return $this;
    }

    /**
     * @param User $subscriber
     * @return Common
     */
    public function addSubscriber(User $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers->add($subscriber);
            if (!$subscriber->getSubscriptions()->contains($this))
            {
                $subscriber->getSubscriptions()->add($this);
            }
        }

        return $this;
    }

    /**
     * @param User $subscriber
     * @return Common
     */
    public function removeSubscriber(User $subscriber): self
    {
        if ($this->subscribers->contains($subscriber)) {
            $this->subscribers->removeElement($subscriber);
            if ($subscriber->getSubscriptions()->contains($this))
            {
                $subscriber->getSubscriptions()->removeElement($this);
            }
        }

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

}
