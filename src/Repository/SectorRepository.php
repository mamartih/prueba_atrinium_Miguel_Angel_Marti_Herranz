<?php

namespace App\Repository;

use App\Entity\Sector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sector|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sector|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sector[]    findAll()
 * @method Sector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sector::class);
    }

    public function BuscarSectores($params=NULL){
        if ($params==NULL) {
        return $this->getEntityManager()
        ->createQuery('SELECT sector.id, sector.nombre
        FROM App:Sector sector');}
        return $this->getEntityManager()
        ->createQuery("SELECT sector.nombre FROM App:Sector sector 
        JOIN App:User_Sector user_sector WHERE user_sector.user = $params 
        AND user_sector.sector = sector.id");
    }

    

    // /**
    //  * @return Sector[] Returns an array of Sector objects
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
    public function findOneBySomeField($value): ?Sector
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
