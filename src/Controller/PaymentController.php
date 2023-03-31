<?php

namespace App\Controller;

use App\Repository\BookingsRepository;
use App\Repository\OfficeRepository;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    /**
     * @throws ApiErrorException
     */
    #[Route('/payment', name: 'app_payment')]
    public function payment(Request $request, OfficeRepository $repository): Response
    {
        // Récupération de l'ID du bureau réservé depuis la session
        $amount_fees = $request->getSession()->get('montant');
        $quantity = $request->getSession()->get('day');
        $reserved_booking = $request->getSession()->get('reserved_booking');

        //$reserved_office = $repository->find($reserved_office_id);
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        $productName = $reserved_booking->getOffice()->getTitle();


        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $amount_fees * 100,
                    'product_data' => [
                        'name' => $productName,
                    ],
                ],
                'quantity' => $quantity,
            ]],

            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_booking_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_details_office', ['id' => $reserved_booking->getOffice()->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        $request->getSession()->remove('reserved_office_id');

        return $this->render('payment/payment.html.twig', [
            'checkout_session_id' => $checkout_session->id,
            'stripe_public_key' => $_SERVER['STRIPE_PUBLIC_KEY'],
            'price' => $amount_fees,
        ]);
    }



    #[Route('/payment_success', name: 'app_payment_success')]
    public function success(): Response
    {
        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment_failed', name: 'app_payment_failed')]
    public function failed(): Response
    {
        return $this->render('payment/failed.html.twig');
    }


}
