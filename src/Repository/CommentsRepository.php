<?php

namespace App\Repository;

use App\Entity\Comments;

use App\Entity\User;

use App\Entity\Series;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }


    public function findCommentSeriesByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.User','u')
            ->where('u.id = :user')
            ->andWhere('c.validated = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getCommentByNoteASC(){
        return $this->createQueryBuilder("c")
            ->orderBy("c.note", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function getCommentByNoteDESC(){
        return $this->createQueryBuilder("c")
            ->orderBy("c.note","DESC")
            ->getQuery()
            ->getResult();
    }

    public function getMoyenneCommentaireValide(){
        return $this->createQueryBuilder("c")
            ->where("c.validated = 1")
            ->getQuery()
            ->getResult();
    }




    // /**
    //  * @return Comments[] Returns an array of Comments objects
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
    public function findOneBySomeField($value): ?Comments
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
