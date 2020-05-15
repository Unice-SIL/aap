<?php


namespace App\Form\DataTransformer;


use App\Entity\Acl;
use App\Repository\AclRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class BaseAclTransformer implements DataTransformerInterface
{

    /**
     * @var AclRepository
     */
    private $aclRepository;

    /**
     * BaseAclTransformer constructor.
     * @param AclRepository $aclRepository
     */
    public function __construct(AclRepository $aclRepository)
    {
        $this->aclRepository = $aclRepository;
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
        $acls = new ArrayCollection();
        foreach ($value as $permission => $users) {

            foreach ($users as $user) {

                $acl = $this->aclRepository->findOneBy([
                    'user' => $user,
                    'permission' => $permission
                ]);

                if (null === $acl) {
                    $acl = new Acl();
                    $acl->setUser($user);
                    $acl->setPermission($permission);
                }

                $acls->add($acl);
            }
        }

        return $acls;
    }
}