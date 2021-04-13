<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function BuscarUsuarios($params=NULL, EntityManager $em){
        if ($params==NULL) {
            
            $qm = $em->createQueryBuilder('user');
            $qm->select('DISTINCT (user.id),user.roles, user.email, sector1.nombre')
                ->from('App:User', 'user')
                ->leftJoin('user.sector', 'user_sector')
                ->leftJoin('user_sector.sector', 'sector1')
                ;
            return $this->getEntityManager()
                ->createQuery($qm->getDql()); 
        }
    }

    public function BuscarUserSectores($params=NULL, EntityManager $em){
        if ($params==NULL) {        
        }
        $qm = $em->createQueryBuilder('user');
        $qm->select('DISTINCT (user.id),user.roles, user.email, sector1.id')
            ->from('App:User', 'user')
            ->leftJoin('user.sector', 'user_sector')
            ->leftJoin('user_sector.sector', 'sector1')
            ->where("user.id=$params")
            ;
        return $this->getEntityManager()
            ->createQuery($qm->getDql())->getResult(); 
    }  

    public function DeleteUserSectores($params=NULL, EntityManager $em){
        if ($params==NULL) {        
        };
        return $this->getEntityManager()
            ->createQuery("DELETE FROM App:User_Sector user_sector WHERE user_sector.user = $params")
            ->getResult(); 
    }  

    

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
