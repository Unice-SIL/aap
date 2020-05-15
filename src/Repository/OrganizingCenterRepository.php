<?php

namespace App\Repository;

use App\Entity\OrganizingCenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrganizingCenter|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizingCenter|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizingCenter[]    findAll()
 * @method OrganizingCenter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizingCenterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizingCenter::class);
    }

    // /**
    //  * @return OrganizingCenter[] Returns an array of OrganizingCenter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrganizingCenter
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getBaseQueryBuilderWithRelations()
    {
        return $this->createQueryBuilder('oc')
            ->leftJoin('oc.acls', 'a')
            ->leftJoin('a.user', 'u')
            ->addSelect('a', 'u')
            ;
    }

    public function findAllWithRelations()
    {

        return $this->getBaseQueryBuilderWithRelations()
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByIdWithRelations(string $id)
    {

        return $this->getBaseQueryBuilderWithRelations()
            ->andWhere('oc.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
