<?php

namespace App\Controller;

use App\Repository\OfficeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(OfficeRepository $officeRepo): Response
    {
        $offices = $officeRepo->findAll();
        return $this->render('home/list.html.twig', [
            'offices' => $offices
        ]);
    }


    #[Route('/map', name: 'app_home_map')]
    public function index(OfficeRepository $officeRepository): Response
    {
        $officesObject = $officeRepository->findAll();

        // Convertir les objets en tableaux associatifs
        $offices = array_map(function ($office) {
            $cover = $office->getImageFilenames();
            return [
                'id' => $office->getId(),
                'price' => number_format($office->getPrice(), 0, ',', ' '),
                'city' => $office->getLocation()->getCity()->getName(),
                'country' => $office->getLocation()->getCity()->getCountry(),
                'latitude' => $office->getLocation()->getCity()->getLatitude(),
                'longitude' => $office->getLocation()->getCity()->getLongitude(),
                'cover' => reset($cover)
            ];
        }, $officesObject);

        return $this->render('home/map.html.twig', [
            'offices' => $offices
        ]);
    }


}
