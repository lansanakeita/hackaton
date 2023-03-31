<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Office;
use App\Entity\OfficeAddress;
use App\Form\AddressFormType;
use App\Form\OfficeAddressType;
use App\Form\OfficeFormType;
use App\Form\UpdatedOfficeAddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfficeAddressController extends AbstractController
{
    #[Route('/create_office', name: 'app_create_office_address')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $office = new Office();
        $address = new Address();

        $formOffice = $this->createForm(OfficeFormType::class, $office);
        $formAddress = $this->createForm(AddressFormType::class, $address);

        $formOfficeAddress = $this->createForm(OfficeAddressType::class);

        $formOfficeAddress->handleRequest($request);

        if ($formOfficeAddress->isSubmitted() && $formOfficeAddress->isValid()) {
            $officeAddress = $formOfficeAddress->getData();

            // Lier l'adresse au bureau
            $officeAddress->getOffice()->setLocation($officeAddress->getAddress());
            $officeAddress->getOffice()->setOwner($this->getUser());

            // Gérer l'upload des images multiples
            $uploadedFiles = $request->files->get('office_address')['office']['images'];
            if ($uploadedFiles) {
                $images = [];

                foreach ($uploadedFiles as $uploadedFile) {
                    $filename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $filename
                    );

                    $images[] = $filename;
                }

                $officeAddress->getOffice()->setImageFilenames($images);
            }


            // Enregistrer les données
            $entityManager->persist($officeAddress->getOffice());
            $entityManager->flush();

            return $this->redirectToRoute('app_create_office_address');
        }

        return $this->render('office_address/create_office.html.twig', [
            'formOffice' => $formOffice->createView(),
            'formAddress' => $formAddress->createView(),
            'formOfficeAddress' => $formOfficeAddress->createView(),
        ]);
    }

    #[Route('/update_office/{id}', name: 'app_update_office_address')]
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        $currentOffice = $entityManager->getRepository(Office::class)->find($request->get('id'));
        $officeAddress = new OfficeAddress();
        $officeAddress->setOffice($currentOffice);
        $officeAddress->setAddress($currentOffice->getLocation());
        $formOfficeAddress = $this->createForm(UpdatedOfficeAddressType::class, $officeAddress);
        $formOfficeAddress->handleRequest($request);

        if ($formOfficeAddress->isSubmitted() && $formOfficeAddress->isValid()) {
            $uploadedFilesUpdated = $request->files->get('updated_office_address')['office']['images'];
            if ($uploadedFilesUpdated) {
                $images = $officeAddress->getOffice()->getImageFilenames();

                foreach ($uploadedFilesUpdated as $uploadedFile) {
                    $filename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $filename
                    );

                    $images[] = $filename;
                }

                $officeAddress->getOffice()->setImageFilenames($images);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_owner_profile');
        }

        return $this->render('office_address/edit_office.html.twig', [
            'formOfficeAddress' => $formOfficeAddress->createView(),
        ]);
    }

    #[Route('/delete_office/{id}', name: 'app_delete_office_address')]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $office = $entityManager->getRepository(Office::class)->find($request->get('id'));
        $entityManager->remove($office);
        $entityManager->flush();
        return $this->redirectToRoute('app_owner_profile');
    }
}
