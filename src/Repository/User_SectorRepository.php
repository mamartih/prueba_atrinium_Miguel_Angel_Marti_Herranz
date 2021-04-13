<?php

namespace App\Repository;


use App\Entity\User_Sector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User_Sector|null find($id, $lockMode = null, $lockVersion = null)
 * @method User_Sector|null findOneBy(array $criteria, array $orderBy = null)
 * @method User_Sector[]    findAll()
 * @method User_Sector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class User_SectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User_Sector::class);
    }

    

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
