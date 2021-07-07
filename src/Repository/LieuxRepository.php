<?php

namespace App\Repository;

use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }

    public function trouverLieux(int $id)
    {
        $dql = "SELECT l.id, l.nomLieu FROM App\Entity\Lieu l
                WHERE l.ville = :id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter(':id', $id);

        return $query->getResult();
    }

    public function detailsLieu(int $id)
    {
        $dql = "SELECT l.rue, l.latitude, l.longitude, v.no_ville, v.codePostal FROM App\Entity\Lieu l
                JOIN l.ville v
                WHERE l.id = :id ";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter(':id', $id);

        return $query->getResult();
    }




    // /**
    //  * @return Lieu[] Returns an array of Lieu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lieu
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}