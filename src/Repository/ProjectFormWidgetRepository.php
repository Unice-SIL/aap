<?php

namespace App\Repository;

use App\Entity\ProjectFormWidget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectFormWidget|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectFormWidget|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectFormWidget[]    findAll()
 * @method ProjectFormWidget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectFormWidgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectFormWidget::class);
    }

    // /**
    //  * @return ProjectFormWidget[] Returns an array of ProjectFormWidget objects
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
    public function findOneBySomeField($value): ?ProjectFormWidget
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getLikeQuery($query, ?string $callOfProjectId)
    {
        return $this->createQueryBuilder('pfw')
            ->leftJoin('pfw.projectFormLayout', 'pfl')
            ->leftJoin('pfl.callOfProject', 'cop')
            ->andWhere('pfw.title LIKE :query')
            ->andWhere('cop.id = :id')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('id', $callOfProjectId)
            ->getQuery()
            ->getResult()
            ;
    }
}
