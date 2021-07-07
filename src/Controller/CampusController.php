<?php
namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/choixCampus", name="choix_campus")
     */
    public function choisirCampus(CampusRepository $campusRepository){
        $campus = $campusRepository->afficherCampus();
        return new JsonResponse([
            'campus' => $campus,
        ]);
    }
}
