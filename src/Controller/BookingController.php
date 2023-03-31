<?php

namespace App\Controller;

use App\Entity\Bookings;
use App\Entity\Office;
use App\Form\BookingType;
use App\Repository\BookingsRepository;
use App\Repository\OfficeRepository;
//use DateInterval;
//use DatePeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Dompdf\Dompdf;
use Dompdf\Options;
//use function PHPUnit\Framework\isEmpty;


class BookingController extends AbstractController
{
    private $manager;
    private $requestStack;
    private $pdfOption;
    private $dompdf;
    public function __construct(
        EntityManagerInterface $manager,
        RequestStack $requestStack,
    ) {
        $this->manager           = $manager;
        $this->requestStack = $requestStack;
        $this->dompdf                   = new Dompdf();
        $this->pdfOption                = new Options();
    }
    #[Route('/details-office/{id}', name: 'app_details_office')]
    public function details(Request $request, OfficeRepository $officeRepo, $id, BookingsRepository $repoBooking): Response
    {
        $booking = new Bookings;
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        $office = $officeRepo->find($id);
        $officeCoordinates = [
            'latitude' => $office->getLocation()->getCity()->getLatitude(),
            'longitude' => $office->getLocation()->getCity()->getLongitude(),
        ];

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->getUser() == null) {
                $this->addFlash('success', 'Veuillez vous connecter avant de reserver svp...');
                return $this->redirectToRoute('app_login');
            } else {
                return $this->booking($request, $booking, $office, $repoBooking);
            }
        }
        return $this->render('booking/details_office.html.twig', [
            'office' => $office,
            'form' => $form->createView(),
            'coordinates' => $officeCoordinates
        ]);
    }

    public function booking(Request $request, Bookings $booking, Office $office, BookingsRepository $repoBooking): Response
    {
        $session = $this->requestStack->getSession();
        $id = $office->getId();
        $isVerify = true;
        $booking->setUser($this->getUser());
        $booking->setOffice($office);

        if ($booking->getEnd() == null) {
            $booking->setEnd($booking->getStart());
        }

        $dataBookings = $repoBooking->findBy(['office' => $office]);
        if ($booking->getSlots() == "Matinée" ||  $booking->getSlots() == "Après-midi") {
            $montant = $office->getPrice() / 2;
        } else {
            $montant = $office->getPrice();
        }

        foreach ($dataBookings as $data) {

            $bookingStart = $booking->getStart();
            $bookingEnd = $booking->getEnd();
            $bookingSlots = $booking->getSlots();
            $dataStart = $data->getStart();
            $dataEnd = $data->getEnd();
            $dataSlots = $data->getSlots();

            if ($bookingStart == $dataStart && $bookingSlots == $dataSlots) {
                $isVerify = false;
            }
            if ($bookingStart == $dataStart && $bookingEnd == $dataEnd && $bookingSlots == $dataSlots) {
                $isVerify = false;
            }
            if ($bookingStart == $dataStart && ($bookingSlots == $dataSlots || $bookingSlots == "Journée")) {
                $isVerify = false;
            }

            if ($bookingStart <= $dataStart && $bookingEnd >= $dataEnd && $bookingSlots == $dataSlots) {
                $isVerify = false;
            }
            if ($bookingStart <= $dataStart && $bookingEnd >= $dataEnd && ($bookingSlots == $dataSlots || $bookingSlots == "Journée")) {
                $isVerify = false;
            }
            if ($bookingStart >= $dataStart && $bookingEnd <= $dataEnd && ($bookingSlots == $dataSlots || $bookingSlots == "Journée")) {
                $isVerify = false;
            }
            // if ($bookingStart >= $dataStart && $bookingEnd >= $dataEnd && $bookingSlots == $dataSlots) {
            //     $isVerify = false;
            // }
        }
        if (!$isVerify) {
            $this->addFlash('success', 'Désolé le bureau est occupé pour ce créneau...');
            return $this->redirectToRoute('app_details_office', ['id' => $id]);
        } else {
            $interval = $booking->getStart()->diff($booking->getEnd());
            if ($interval->d == 0 && $booking->getSlots() == "Matinée" || $interval->d == 0 && $booking->getSlots() == "Après-midi") {
                $day = 1;
                $session->set('day', $day);
                $session->set('office', $office);
                $session->set('montant', $montant);
                $session->set('booking', $booking);

                $request->getSession()->set('reserved_booking', $booking);
                $request->getSession()->set('day', $day);
                $request->getSession()->set('montant', $montant);

                $this->manager->persist($booking);
                $this->manager->flush();
                return $this->redirectToRoute('app_payment');
            } else if ($interval->d == 0 && $booking->getSlots() == "Journée") {
                $day = 1;

                $session->set('day', $day);
                $session->set('office', $office);
                $session->set('montant', $montant);
                $session->set('booking', $booking);

                $request->getSession()->set('reserved_booking', $booking);
                $request->getSession()->set('day', $day);
                $request->getSession()->set('montant', $montant);

                $this->manager->persist($booking);
                $this->manager->flush();
                return $this->redirectToRoute('app_payment');
            } else if ($interval->d != 0 && $booking->getSlots() == "Matinée" || $interval->d != 0 && $bookingSlots == "Après-midi") {

                $day = $interval->d++;
                $session->set('office', $office);
                $session->set('booking', $booking);
                $session->set('day', $day);
                $session->set('montant', $montant);

                $request->getSession()->set('reserved_booking', $booking);
                $request->getSession()->set('day', $day);
                $request->getSession()->set('montant', $montant);
                $this->manager->persist($booking);
                $this->manager->flush();
                return $this->redirectToRoute('app_payment');
            } else if ($interval->d != 0 && $booking->getSlots() == "Journée") {
                $day = $interval->d++;

                $session->set('day', $day);
                $session->set('office', $office);
                $session->set('montant', $montant);
                $session->set('booking', $booking);

                $request->getSession()->set('reserved_booking', $booking);
                $request->getSession()->set('day', $day);
                $request->getSession()->set('montant', $montant);
                $this->manager->persist($booking);
                $this->manager->flush();
                return $this->redirectToRoute('app_payment');
            }
        }
        return $this->redirectToRoute('app_details_office', ['id' => $id]);
    }



    #[Route('/booking-success', name: 'app_booking_success')]
    public function bookingSuccess(Request $request, OfficeRepository $repository): Response
    {
        $booking = $request->getSession()->get('reserved_booking');
        $day = $request->getSession()->get('day');
        $montant = $request->getSession()->get('montant');
        $this->addFlash('success', 'Reservation effectué avec succès...');
        return $this->render('booking/booking_success.html.twig', [
            'booking' => $booking,
            'day' => $day,
            'montant' => $montant,
        ]);
    }

    #[Route('/download', name: 'app_download')]
    public function download(): Response
    {
        $session = $this->requestStack->getSession();
        $day = $session->get('day');
        $montant = $session->get('montant');
        $office = $session->get('office');
        $booking = $session->get('booking');
        $price = $session->get('price');
        $ttc = $session->get('ttc');

        // la police par défaut 
        $this->pdfOption->set('defaultFont', 'Arial');
        $this->pdfOption->setIsRemoteEnabled(true);

        // On instancie le Dompdf
        $this->dompdf->setOptions($this->pdfOption);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => FALSE,
                'verify_peer_name'  => FALSE,
                'allow_self_signed' => TRUE,
            ]
        ]);
        $this->dompdf->setHttpContext($context);

        // on génère le html 
        $html = $this->renderView('booking/download.html.twig', [
            'office' => $office,
            'montant' => $montant,
            'booking' => $booking,
            'ttc' => $ttc,
            'day' => $day,
            'price' => $price
        ]);
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('a4,', 'landscape');
        $this->dompdf->render();

        // on génère le nom du fichier
        $fichier = 'reservation' . '.pdf';

        // on envoie le pdf au navigateur 
        $this->dompdf->stream($fichier, [
            'Attachment'    => true
        ]);
        return new Response();
    }

    #[Route('/delete/{id}', name: 'app_delete_booking')]
    public function delete($id, BookingsRepository $repoBooking): Response
    {
        $booking = $repoBooking->find($id);
        $this->manager->remove($booking);
        $this->manager->flush();
        return $this->redirectToRoute('app_booking_in_progress');
    }

    #[Route('/historique/booking', name: 'app_historique_booking')]
    public function historique(BookingsRepository $repoBooking): Response
    {
        $bookings = $repoBooking->findAll();
        return $this->render('booking/historique.html.twig', [
            'bookings' => $bookings
        ]);
    }
    #[Route('/detail-download/{id}', name: 'app_detail_download')]
    public function detail_download($id, BookingsRepository $repoBooking): Response
    {
        $details = $repoBooking->find($id);

        // la police par défaut 
        $this->pdfOption->set('defaultFont', 'Arial');
        $this->pdfOption->setIsRemoteEnabled(true);

        // On instancie le Dompdf
        $this->dompdf->setOptions($this->pdfOption);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => FALSE,
                'verify_peer_name'  => FALSE,
                'allow_self_signed' => TRUE,
            ]
        ]);

        $this->dompdf->setHttpContext($context);

        // on génère le html 
        $html = $this->renderView('booking/detail_download.html.twig', [
            'details' => $details,

        ]);
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('a4,', 'landscape');
        $this->dompdf->render();

        // on génère le nom du fichier
        $fichier = 'reservation' . '.pdf';

        // on envoie le pdf au navigateur 
        $this->dompdf->stream($fichier, [
            'Attachment'    => true
        ]);
        return new Response();
    }
}
