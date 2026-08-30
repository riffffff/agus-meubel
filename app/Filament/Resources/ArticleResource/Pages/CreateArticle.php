<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var ImageService $imageService */
        $imageService = app(ImageService::class);

        if (!empty($data['image']) && is_string($data['image'])) {
            $data['image'] = $imageService->processUploadedPath($data['image'], 'articles') ?? $data['image'];
        }

        return $data;
    }
}
