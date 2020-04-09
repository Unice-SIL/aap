<?php

namespace App\Repository;

use App\Entity\ProjectFormLayout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectFormLayout|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectFormLayout|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectFormLayout[]    findAll()
 * @method ProjectFormLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectFormLayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectFormLayout::class);
    }

    // /**
    //  * @return ProjectFormLayout[] Returns an array of ProjectFormLayout objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectFormLayout
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
