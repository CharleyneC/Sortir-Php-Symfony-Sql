<?php

namespace App\Controller;

use App\Repository\LieuxRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/searchLieu/{id}", name="search_lieu")
     */
    public function searchLieu(LieuxRepository $lieuxRepository, $id): Response
    {
        $lieux = $lieuxRepository->trouverLieux($id);
        return new JsonResponse([
            'lieux' => $lieux
        ]);
    }

    /**
     * @Route("/detailsLieu/{id}", name="details_lieu")
     */
    public function detailsLieu(LieuxRepository $lieuxRepository, $id): Response
    {
        $detailsLieu = $lieuxRepository->detailsLieu($id);
        return new JsonResponse([
            'detailsLieu' => $detailsLieu,
        ]);
    }
}
