<?php

namespace App\Observers;

use App\Models\Article;
use App\Services\ImageService;

class ArticleObserver
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function saved(Article $article): void
    {
        if (!$article->wasRecentlyCreated) {
            $this->cleanupOldImages($article);
        }
    }

    public function deleted(Article $article): void
    {
        if (!empty($article->image)) {
            try {
                $this->imageService->deleteIfExists($article->image);
            } catch (\Throwable) {
            }
        }
    }

    private function cleanupOldImages(Article $article): void
    {
        $fields = ['image'];

        foreach ($fields as $field) {
            if (!$article->isDirty($field)) {
                continue;
            }

            $oldValue = $article->getOriginal($field);
            $newValue = $article->getAttribute($field);

            if (
                !empty($oldValue)
                && $oldValue !== $newValue
            ) {
                try {
                    $this->imageService->deleteIfExists($oldValue);
                } catch (\Throwable) {
                }
            }
        }
    }
}
