<?php

namespace App\Repository;

use App\Entity\Episodes;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episodes::class);
    }

    public function getAllSaison(Series $series){
        return $this->createQueryBuilder('e')
            ->where("e.Series = :series")
            ->setParameter('series', $series)
            ->orderBy('e.season', 'ASC')
            ->addOrderBy('e.number', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAllEpisodesSaison(Series $series, $saison){
        return $this->createQueryBuilder('e')
            ->where("e.Series = :series")
            ->andWhere("e.season = :saison")
            ->setParameter("series", $series)
            ->setParameter("saison", $saison)
            ->orderBy('e.season','ASC')
            ->addOrderBy('e.number', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getEpisodesYouWant($ep){
        return $this->createQueryBuilder('e')
            ->where("e.name LIKE :ep")
            ->setParameter('ep', '%'.$ep.'%')
            ->getQuery()
            ->getResult()
            ;

    }


    // /**
    //  * @return Series[] Returns an array of Series objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Series
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
