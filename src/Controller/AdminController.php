<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/contacts', name: 'contact_list')]
    public function contactList(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAllOrdererByCreatedAtDesc();

        return $this->render('contact/list.html.twig', [
            'contacts' => $contacts
        ]);
    }
}
