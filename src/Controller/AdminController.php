<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/articles/add', name: 'article_add')]
    public function articleAdd(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', "L'article a été créé avec succès.");

            return $this->redirectToRoute('article_item', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/add.html.twig', [
            'articleForm' => $form
        ]);
    }

    #[Route('/articles/edit/{id}', name: 'article_edit')]
    public function articleEdit(
        Request $request,
        Article $article,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ARTICLE_EDIT', $article);

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/contacts', name: 'contact_list')]
    public function contactList(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAllOrdererByCreatedAtDesc();

        return $this->render('contact/list.html.twig', [
            'contacts' => $contacts
        ]);
    }
}
