<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api')]
final class ApiController extends AbstractController
{
    #[Route('/articles', name: 'api_articles')]
    public function articles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllVisible();

        return $this->json($articles, context: ['groups' => ['articles:list']]);
    }

    #[Route('/articles/{id}', name: 'api_article_item')]
    public function articleItem(Article $article, UrlGeneratorInterface $urlGenerator): Response
    {
        if (!$article->isVisible()) {
            throw $this->createNotFoundException();
        }

        return $this->json([
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'createdAt' => $article->getCreatedAt()->format('Y-m-d'),
            'category' => $article->getCategory()->getName(),
            'tags' => $article->getTags()->map(static fn (Tag $tag) => $tag->getLabel())->toArray(),
            'url' => $urlGenerator->generate('article_item', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
