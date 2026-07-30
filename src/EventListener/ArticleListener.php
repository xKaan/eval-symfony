<?php

namespace App\EventListener;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Article::class)]
class ArticleListener {
    public function __construct(private SluggerInterface $slugger) {
    }

    public function prePersist(Article $article, LifecycleEventArgs $args): void {

        // Si on ne met pas ces conditions, lors de la création des fixtures, ça va réecrire par dessus les valeurs.
        // Or on avait mis pour les fixtures une range de 2 ans pour le createdAt des articles
        if ($article->getCreatedAt() === null) {
            $article->setCreatedAt(new \DateTimeImmutable());
        }

        if ($article->getSlug() === null) {
            $article->setSlug(strtolower($this->slugger->slug($article->getTitle())));
        }
    }
}
