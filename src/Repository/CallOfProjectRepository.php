<?php

namespace App\Repository;

use App\Entity\CallOfProject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    /**
     * @param string $id
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getCallOfProjectForZip(string $id) {
        return $this->createQueryBuilder('cop')
            ->leftJoin('cop.projects', 'p')
            ->leftJoin('p.projectContents', 'pc')
            ->leftJoin('pc.projectFormWidget', 'pfw')
            ->addSelect('p', 'pc', 'pfw')
            ->andWhere('cop.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLikeQuery($query)
    {
        return $this->createQueryBuilder('cop')
            ->andWhere('cop.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult()
            ;
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

    /**
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getLastCode (): int
    {
        $query = $this->createQueryBuilder('cop')
            ->select('MAX(cop.code) AS max_code')
              ->getQuery()
            ->getSingleResult();
        return $query['max_code'] ?? 0;
    }
}
