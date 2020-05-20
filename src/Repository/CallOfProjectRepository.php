<?php

namespace App\Repository;

use App\Entity\CallOfProject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method CallOfProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallOfProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallOfProject[]    findAll()
 * @method CallOfProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallOfProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallOfProject::class);
    }

    public function getByUserAndNameLikeQuery(?UserInterface $user, ?string $query)
    {
        return $this->createQueryBuilder('cop')
            ->andWhere('cop.createdBy = :user')
            ->setParameter('user', $user)
            ->andWhere('cop.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getIfUserHasOnePermissionAtLeast(User $user,array $options = [])
    {
        $qb = $this->createQueryBuilder('cop')
            ->leftJoin('cop.acls', 'copacls')
            ->leftJoin('cop.organizingCenter', 'oc')
            ->leftJoin('oc.acls', 'ocacls')
            ->andWhere('copacls.user = :user')
            ->orWhere('ocacls.user = :user')
            ->setParameter('user', $user)
            ->orderBy('cop.createdAt', 'DESC')
        ;

        if (isset($options['limit'])) {
            $qb->setMaxResults($options['limit']);
        }

        return $qb->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return CallOfProject[] Returns an array of CallOfProject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CallOfProject
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
