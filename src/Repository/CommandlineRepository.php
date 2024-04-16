<?php

namespace App\Repository;

use App\Entity\Commandline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commandline>
 *
 * @method Commandline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commandline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commandline[]    findAll()
 * @method Commandline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandlineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandline::class);
    }

//    /**
//     * @return Commandline[] Returns an array of Commandline objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commandline
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
