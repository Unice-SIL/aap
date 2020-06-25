<?php

namespace App\Repository;

use App\Entity\Dictionary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dictionary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dictionary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dictionary[]    findAll()
 * @method Dictionary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DictionaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dictionary::class);
    }

    // /**
    //  * @return Dictionary[] Returns an array of Dictionary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dictionary
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
