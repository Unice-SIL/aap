<?php

namespace App\Repository;

use App\Entity\ProjectContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectContent[]    findAll()
 * @method ProjectContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectContent::class);
    }


    public function getNotNullProjectContentByWidgetClasses(array $widgetClasses = [])
    {

        return $this->createQueryBuilder('pc')
            ->innerJoin('pc.projectFormWidget', 'pfw')
            ->andWhere('pc.content IS NOT NULL')
            ->andWhere('pfw.widgetClass IN (:widgetClasses)')
            ->setParameter('widgetClasses', $widgetClasses)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?ProjectContent
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
