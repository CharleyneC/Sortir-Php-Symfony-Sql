<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/villes", name="afficher_villes")
     */
    public function afficherVilles(VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->afficherVilles();
        return new JsonResponse([
            'villes' => $villes,
        ]);
    }
}
