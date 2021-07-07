<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    //retrouver une sortie par mot-clé
    public function search(string $keyword)
    {
        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                        s.etatsortie, o.pseudo, l.nomLieu
                                        FROM App\Entity\Sorties s
                                        JOIN s.organisateur o
                                        JOIN s.lieu l
                                        WHERE s.nom LIKE :keyword")
            ->setParameter(':keyword', '%' . $keyword . '%');

        return $dql->getResult();
    }


    //retrouver une sortie par selection de campus

    public function searchByCampus(string $option)
    {
        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                        s.etatsortie, o.pseudo, l.nomLieu
                                        FROM App\Entity\Sorties s
                                        JOIN s.organisateur o
                                        JOIN s.siteOrganisateur so
                                        JOIN s.lieu l                                       
                                        WHERE so.nom LIKE :option")
            ->setParameter(':option', "%" . $option . "%");

        return $dql->getResult();
    }

    //trouver une sortie entre 2 dates
    public function searchByDate(string $choixopen, string $choixclose)
    {

        $open = str_replace(' ', '-', $choixopen);
        $fromdebut = new \DateTime($open);

        $close = str_replace(' ', '-', $choixclose);
        $tocloture = new \DateTime($close);


        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                    s.etatsortie, o.pseudo, l.nomLieu
                                    FROM App\Entity\Sorties s
                                    JOIN s.organisateur o
                                    JOIN s.lieu l                                     
                                    WHERE s.datedebut >= :fromdebut
                                    AND s.datecloture <= :tocloture")
            ->setParameter(':fromdebut', $fromdebut)
            ->setParameter(':tocloture', $tocloture);

        return $dql->getResult();
    }


    public function findTableau()
    {

        $dql = $this->getEntityManager()
        ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                    s.etatsortie, o.pseudo, l.nomLieu
                                    FROM App\Entity\Sorties s
                                    JOIN s.lieu l
                                    JOIN s.organisateur o");

        return $dql->getResult();
    }

    public function searchByCheckboxOrga(int $choixOrga)
    {
        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                    s.etatsortie, o.pseudo, l.nomLieu
                                    FROM App\Entity\Sorties s                                    
                                    JOIN s.organisateur o 
                                    JOIN s.lieu l                                 
                                    WHERE o.idParticipant = :choixOrga")
            ->setParameter(':choixOrga', $choixOrga);
        return $dql->getResult();
    }

    public function searchByCheckboxInscrit(int $choixInscrit)
    {
        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                    s.etatsortie, o.pseudo, l.nomLieu
                                    FROM App\Entity\Sorties s                                    
                                    JOIN s.organisateur o
                                    JOIN s.lieu l
                                    JOIN s.estInscrit e                                         
                                    WHERE e.idParticipant = :choixInscrit")
            ->setParameter(':choixInscrit', $choixInscrit);

        return $dql->getResult();
    }

    public function searchByCheckboxNonInscrit(int $choixNonInscrit)
    {
        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                    s.etatsortie, o.pseudo, l.nomLieu
                                    FROM App\Entity\Sorties s
                                    JOIN s.organisateur o
                                    JOIN s.estInscrit e
                                    JOIN s.lieu l                                         
                                    WHERE e.idParticipant != :choixNonInscrit")
            ->setParameter(':choixNonInscrit', $choixNonInscrit);

        return $dql->getResult();
    }

    public function searchByCheckboxDate(string $choixD)
    {
        $close = str_replace(' ', '-', $choixD);
        $datepasse = new \DateTime($close);

        $dql = $this->getEntityManager()
            ->createQuery("SELECT s.id, s.nom, s.datedebut, s.datecloture, s.nbinscriptionsmax,
                                    s.etatsortie, o.pseudo, l.nomLieu
                                    FROM App\Entity\Sorties s
                                    JOIN s.lieu l
                                    JOIN s.organisateur o                                     
                                    WHERE s.datecloture < :datepasse")
            ->setParameter(':datepasse', $datepasse);

        return $dql->getResult();
    }

}
