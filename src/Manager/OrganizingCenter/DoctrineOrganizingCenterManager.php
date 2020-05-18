<?php

namespace App\Manager\OrganizingCenter;

use App\Entity\OrganizingCenter;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineOrganizingCenterManager extends AbstractOrganizingCenterManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineOrganizingCenterManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(OrganizingCenter $organizingCenter)
    {
        $this->em->persist($organizingCenter);
        $this->em->flush();
    }

    public function update(OrganizingCenter $organizingCenter)
    {
        $this->em->flush();
    }

    public function delete(OrganizingCenter $organizingCenter)
    {
        $this->em->remove($organizingCenter);
        $this->em->flush();
    }

}