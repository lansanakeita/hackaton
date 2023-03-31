<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\BookingsRepository;
use App\Repository\OfficeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/account/my_profile', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/account/profile/edit', name: 'app_edit_profil')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user =  $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Les informations de votre profil ont bien été modifiées.'
            );
            return $this->redirectToRoute('app_profil');
        }
        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/offices', name: 'app_owner_profile')]
    public function showProfile(Request $request, OfficeRepository $officeRepository, EntityManagerInterface $entityManager): Response
    {
        $userId = $this->getUser()->getId();
        $offices = $officeRepository->findBy(['owner' => $userId]);
        return $this->render('profil/user_profile.html.twig', [
            'offices' => $offices
        ]);
    }

    #[Route('/booking-in-progress', name: 'app_booking_in_progress')]
    public function renterBookingInProgress(BookingsRepository $repository): Response
    {
        $userId = $this->getUser()->getId();
        $bookings = $repository->findCurrentBookings();
        return $this->render('profil/booking_in_progress.html.twig', ['bookings' => $bookings]);
    }
}
