<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'articles_list')]
    public function list(ArticleRepository $articleRepository): Response
    {
        // Récupère la donnée
        $articles = $articleRepository->findRecentVisible(Article::LIST_LIMIT);

        // Je passe la donnée à la vue
        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/{slug}', name: 'article_item')]
    public function item(Article $article): Response
    {
        return $this->render('article/item.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/articles/add', name: 'article_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $article = new Article();
        $article->setAuthor($this->getUser());

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
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
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
}
