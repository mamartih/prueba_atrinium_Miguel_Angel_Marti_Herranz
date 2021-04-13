<?php

namespace App\Repository;

use App\Entity\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Empresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empresa[]    findAll()
 * @method Empresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function BuscarEmpresas($params=NULL){
        if ($params==NULL) {
            return $this->getEntityManager()
        ->createQuery('SELECT empresa.id, empresa.nombre, empresa.telefono, empresa.email, 
        sector.nombre as sector2 FROM App:Empresa empresa JOIN App:Sector sector 
        WHERE sector.id = empresa.sector');
        }
        return $this->getEntityManager()
        ->createQuery("SELECT empresa.id, empresa.nombre, empresa.telefono, 
        empresa.email, sector.nombre as sector2 FROM App:Empresa empresa 
        JOIN App:Sector sector JOIN App:User_Sector user_sector 
        WHERE sector.id = empresa.sector AND user_sector.user = $params 
        AND user_sector.sector = sector.id");
    }

    // /**
    //  * @return Empresa[] Returns an array of Empresa objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Empresa
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
