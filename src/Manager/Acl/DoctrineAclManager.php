<?php

namespace App\Manager\Acl;

use App\Entity\Acl;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAclManager extends AbstractAclManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineAclManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Acl $acl)
    {
        $this->em->persist($acl);
        $this->em->flush();
    }

    public function update(Acl $acl)
    {
        $this->em->flush();
    }

    public function delete(Acl $acl)
    {
        $this->em->remove($acl);
        $this->em->flush();
    }

}