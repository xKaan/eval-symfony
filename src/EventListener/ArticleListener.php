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
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setSlug(strtolower($this->slugger->slug($article->getTitle())));
    }
}
