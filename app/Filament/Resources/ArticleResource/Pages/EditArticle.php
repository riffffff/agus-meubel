<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var ImageService $imageService */
        $imageService = app(ImageService::class);

        if (!empty($data['image']) && is_string($data['image'])) {
            $data['image'] = $imageService->processUploadedPath($data['image'], 'articles') ?? $data['image'];
        }

        return $data;
    }
}
