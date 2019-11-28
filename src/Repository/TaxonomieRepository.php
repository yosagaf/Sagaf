<?php

namespace App\Repository;

use App\Entity\Taxonomie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Taxonomie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxonomie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxonomie[]    findAll()
 * @method Taxonomie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxonomie::class);
    }

    // /**
    //  * @return Taxonomie[] Returns an array of Taxonomie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Taxonomie
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
