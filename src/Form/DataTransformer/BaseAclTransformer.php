<?php


namespace App\Form\DataTransformer;


use App\Entity\Acl;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class BaseAclTransformer implements DataTransformerInterface
{

    private $entityRecipient;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * BaseAclTransformer constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @return mixed
     */
    public function getEntityRecipient()
    {
        return $this->entityRecipient;
    }

    /**
     * @param mixed $entityRecipient
     */
    public function setEntityRecipient($entityRecipient): self
    {
        $this->entityRecipient = $entityRecipient;

        return $this;
    }

    public function transform($value)
    {
        $mapping = [];

        foreach ($value as $acl) {
            $mapping[$acl->getPermission()][] = $acl->getUser();
        }

        return $mapping;
    }

    public function reverseTransform($value)
    {

        $newAcls = new ArrayCollection();
        $oldAcls = $this->getEntityRecipient()->getAcls();

        foreach ($value as $permission => $users) {

            foreach ($users as $user) {

                /** @var Acl|null $acl */
                $acl = $this->getEntityRecipient()->getAcls()->filter(function ($acl) use ($permission, $user) {
                    return $acl->getPermission() === $permission and $acl->getUser() === $user;
                })->first();

                if (!$acl) {
                    $acl = new Acl();
                    $acl->setUser($user);
                    $acl->setPermission($permission);
                }

                $newAcls->add($acl);
            }
        }

        $aclstoRemove = [];

        foreach ($oldAcls as $oldAcl) {
            $aclMatched = array_filter($newAcls->toArray(), function ($nacl) use ($oldAcl) {
               return $nacl->getUser() ===  $oldAcl->getUser() and $nacl->getPermission() === $oldAcl->getPermission();
            });

            if (count($aclMatched) == 0) {
                $this->em->remove($oldAcl);
            }
        }

        return $newAcls;
    }
}