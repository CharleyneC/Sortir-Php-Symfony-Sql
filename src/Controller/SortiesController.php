<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\CampusRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortiesRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function MongoDB\BSON\toJSON;

class SortiesController extends AbstractController
{
    /**
     * @Route("/sorties/register", name="add_sortie")
     */

    public function registerEvent(Request $request, EntityManagerInterface $emi, LieuxRepository $lieuxRepository): Response
    {
        $sortie = new Sorties();
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setSiteOrganisateur($this->getUser()->getCampus());
            $sortie->setLieu($lieuxRepository->find($request->request->get('lieux')));
            $emi = $this->getDoctrine()->getManager();
            $emi->persist($sortie);
            $emi->flush();
            $this->addFlash('success', 'The event has been saved !');
            return $this->redirectToRoute('sortie_afficher', ['id' => $sortie->getId()]);
        }
        return $this->render('sorties/creerSortie.html.twig', [
            'sortieForm' => $form->createView(),

        ]);
    }

    /**
     * @Route("/sorties/afficherSortie/{id}", name="sortie_afficher", requirements={"id": "\d+"})
     */
    public
    function afficherSortie(int $id, SortiesRepository $sortiesRepository, LieuxRepository $lieuxRepository,
                            CampusRepository $campusRepository, Request $request): Response
    {

        $sortie = $sortiesRepository->find($id);
        $campus = $campusRepository->find($sortie->getSiteOrganisateur());
        $lieu = $sortie->getLieu();
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setLieu($lieuxRepository->find($request->request->get('lieux')));
            $lieu = $sortie->getLieu();
            $emi = $this->getDoctrine()->getManager();
            $emi->persist($sortie);
            $emi->flush();
            $this->addFlash('success', 'The event has been saved !');

        }

        return $this->render('sorties/afficherSortie.html.twig', [
            'sortieForm' => $form->createView(),
            'sortie' => $sortie,
            'lieu' => $lieu,
            'campus' => $campus
        ]);

    }

    /**
     * @Route("/sorties/delete/{id}", name="sortie_delete", requirements={"id":"\d+"})
     */
    public function delete($id)
    {
        $qb = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->delete(Sorties::class, 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
        return new JsonResponse([
            "status" => "deleted",
        ], 200);
    }

    /**
     * @Route ("/sorties/filtrerSorties", name="sortie_filtrer")
     */
    public function userCo(SortiesRepository $sortiesRepository,
                           CampusRepository $campusRepository,
                           ParticipantRepository $participantRepository,
                           Request $request): Response

    {
        $idUser = $participantRepository->findAll();
        $idCampus = $campusRepository->findAll();
        $idSortie = $sortiesRepository->findAll();

        return $this->render('sorties/accueil.html.twig', [
            "estInscrit" => $idUser,
            "organisateur" => $idUser,
            "siteOrganisateur" => $idCampus,
            "sorties" => $idSortie
        ]);

    }

    /**
     * @Route("/afficherTab/", name="afficher_tableau")
     */
    public function afficherTableau(SortiesRepository $sortiesRepository): Response{

        $sorties = $sortiesRepository->findTableau();

        return new JsonResponse([
            'sortie' => $sorties,
        ]);
    }

    /**
     * @Route ("sorties/search/{keyword}", name="filtrer_keyword")
     */
    public function search($keyword, Request $request, SortiesRepository $sortiesRepository): Response
    {
        $sorties = $sortiesRepository->search($keyword);

        return new JsonResponse([
            'sortie' => $sorties
        ]);
    }

    /**
     * @Route ("/majCampus/{option}", name="campus_maj")
     */
    public function findCampusSorties($option, SortiesRepository $sortiesRepository, Request $request): Response
    {
        $sorties = $sortiesRepository->searchByCampus($option);

        return new JsonResponse([
            'sortie' => $sorties
        ]);
    }

    /**
     * @Route("/searchDate/{open}/{close}", name="filtre_date")
     */
    public function findDate(SortiesRepository $sortiesRepository, Request $request, $open, $close): Response
    {
        $sorties = $sortiesRepository->searchByDate($open, $close);

        return new JsonResponse([
            'sortie' => $sorties
        ]);
    }

    /**
     * @Route("/searchCheckboxOrga/{choixOrga}", name="filtre_checkboxOrga")
     */
    public function findCheckOrga(SortiesRepository $sortiesRepository, Request $request, $choixOrga):Response{

        $sorties = $sortiesRepository->searchByCheckboxOrga($choixOrga);

        return new JsonResponse([
            'sortie'=>$sorties
        ]);
    }

    /**
     * @Route("/searchCheckboxInscrit/{choixInscrit}", name="filtre_checkboxInscrit")
     */
    public function findCheckInscrit(SortiesRepository $sortiesRepository, Request $request, $choixInscrit):Response{

        $sorties = $sortiesRepository->searchByCheckboxInscrit($choixInscrit);

        return new JsonResponse([
            'sortie'=>$sorties
        ]);
    }

    /**
     * @Route("/searchCheckboxNonInscrit/{choixNonInscrit}", name="filtre_checkboxNonInscrit")
     */
    public function findCheckNonInscrit(SortiesRepository $sortiesRepository, Request $request, $choixNonInscrit):Response{

        $sorties = $sortiesRepository->searchByCheckboxNonInscrit($choixNonInscrit);

        return new JsonResponse([
            'sortie' => $sorties
        ]);

    }

    /**
     * @Route("/searchCheckboxDate/{choixD}", name="filtre_checkboxByDate")
     */
    public function findCheckDate(SortiesRepository $sortiesRepository, Request $request, $choixD): Response
    {

        $sorties = $sortiesRepository->searchByCheckboxDate($choixD);

        return new JsonResponse([
            'sortie' => $sorties
        ]);
    }

    /**
     * @Route("/sinscrireSortie/{idSortie}", name ="sinscrire_sortie")
     */
    public function sinscrireSortie(ParticipantRepository $participantRepository,
                                    SortiesRepository $sortiesRepository,
                                    Request $request, $idSortie)
    {
            $sortie = $sortiesRepository->find($idSortie);
            $sortie->addEstInscrit($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_filtrer');

        }
}
