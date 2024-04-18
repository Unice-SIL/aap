<?php

namespace App\Repository;

use App\Entity\Acl;
use App\Entity\CallOfProject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
        $qb = $this->createQueryBuilder('cop');
        return $qb
            ->leftJoin('cop.acls', 'copa')
            ->leftJoin('copa.groupe', 'copag')
            ->leftJoin('copag.members', 'copagm')
            ->leftJoin('cop.organizingCenter', 'oc')
            ->leftJoin('oc.acls', 'oca')
            ->leftJoin('oca.groupe', 'ocag')
            ->leftJoin('ocag.members', 'ocagm')
            ->where($qb->expr()->like('cop.name', ':query'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('copa.permission', ':permission'),
                        $qb->expr()->orX(
                            $qb->expr()->eq('copa.user', ':user'),
                            $qb->expr()->eq('copagm.id', ':user')
                        )
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->eq('oca.permission', ':permission'),
                            $qb->expr()->orX(
                                $qb->expr()->eq('oca.user', ':user'),
                                $qb->expr()->eq('ocagm.id', ':user')
                            )
                        )
                    )
                )
            )
            ->setParameter('user', $user)
            ->setParameter('permission', Acl::PERMISSION_ADMIN)
            ->setParameter('query', '%' . $query . '%')
            ->distinct()
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getNumberMax (): int
    {
        $query = $this->createQueryBuilder('cop')
            ->select('MAX(cop.number) AS max_number')
            ->getQuery()
            ->getSingleResult();
        return $query['max_number'] ?? 0;
    }
}
