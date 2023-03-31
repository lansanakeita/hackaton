<?php

namespace App\Controller;

use App\Repository\OfficeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    public function searchBar()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('handleSearch'))
            ->add(
                'query',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-inline',
                        'placeholder' => 'Où trouver votre prochain bureau ?'
                    ]
                ]
            )
            ->getForm();
        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/handleSearch", name="handleSearch")
     * @param Request $request
     */
    public function handleSearch(Request $request, OfficeRepository $repo)
    {
        $query = $request->request->all('form')['query'];
        if ($query) {
            $offices = $repo->findOfficesByName($query);
        }
        return $this->render('search/index.html.twig', [
            'offices' => $offices
        ]);
    }
}
