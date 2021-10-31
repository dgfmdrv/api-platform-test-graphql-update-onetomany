<?php

namespace App\Repository;

use App\Entity\Propietario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Propietario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Propietario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Propietario[]    findAll()
 * @method Propietario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropietarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Propietario::class);
    }

    // /**
    //  * @return Propietario[] Returns an array of Propietario objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Propietario
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
