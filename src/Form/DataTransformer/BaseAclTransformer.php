<?php


namespace App\Form\DataTransformer;


use App\Entity\Acl;
use App\Entity\Group;
use App\Entity\User;
use App\Manager\Acl\AclManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validation;

class BaseAclTransformer implements DataTransformerInterface
{

    private $entityRecipient;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var AclManagerInterface
     */
    private $aclManager;

    /**
     * BaseAclTransformer constructor.
     * @param EntityManagerInterface $em
     * @param AclManagerInterface $aclManager
     */
    public function __construct(EntityManagerInterface $em, AclManagerInterface $aclManager)
    {
        $this->em = $em;
        $this->aclManager = $aclManager;
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
            $mapping[$acl->getPermission()][$acl->getEntity()->getId()] =  $acl->getUser();
        }

        return $mapping;
    }

    public function reverseTransform($value)
    {

        $newAcls = new ArrayCollection();
        $oldAcls = $this->getEntityRecipient()->getAcls();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($value as $permission => $ids) {

            foreach ($ids as $id) {

                $validator = Validation::createValidator();

                if (0 !== count($validator->validate($id,  new Uuid())))
                {
                    continue;
                }


                if (($entity = $this->em->getRepository(Group::class)->findOneById($id)) instanceof Group) {
                    $property = 'groupe';
                } elseif (($entity = $this->em->getRepository(User::class)->findOneById($id)) instanceof User) {
                    $property = 'user';
                } else {
                    throw new TransformationFailedException(sprintf(
                        'An entity with id "%s" does not exist!',
                        $id
                    ));
                }

                /** @var Acl|null $acl */
                $acl = $this->getEntityRecipient()->getAcls()->filter(
                    function ($acl) use ($permission, $entity, $propertyAccessor) {
                        return $acl->getPermission() === $permission and $acl->getEntity() === $entity;
                    }
                )->first();

                if (!$acl) {
                    $acl = $this->aclManager->create();
                    $propertyAccessor->setValue($acl, $property, $entity);
                    $acl->setPermission($permission);
                }

                $newAcls->add($acl);
            }
        }

        $aclstoRemove = [];

        foreach ($oldAcls as $oldAcl) {
            $aclMatched = array_filter($newAcls->toArray(), function ($nacl) use ($oldAcl, $propertyAccessor) {
                $property = $nacl instanceof Group ? 'groupe' : 'user';
                return $propertyAccessor->getValue($nacl, $property) ===  $propertyAccessor->getValue($oldAcl, $property)
                    and $nacl->getPermission() === $oldAcl->getPermission();
            });

            if (count($aclMatched) == 0) {
                $this->em->remove($oldAcl);
            }
        }

        return $newAcls;
    }
}