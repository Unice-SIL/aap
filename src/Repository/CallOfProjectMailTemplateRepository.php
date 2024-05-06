<?php

namespace App\Repository;

use App\Entity\CallOfProjectMailTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallOfProjectMailTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallOfProjectMailTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallOfProjectMailTemplate[]    findAll()
 * @method CallOfProjectMailTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallOfProjectMailTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallOfProjectMailTemplate::class);
    }
}
