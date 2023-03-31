<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'app_politique')]
    public function politique(): Response
    {
        return $this->render('document/politique.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('document/cgu.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions')]
    public function mention(): Response
    {
        return $this->render('document/mentions.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }
}
