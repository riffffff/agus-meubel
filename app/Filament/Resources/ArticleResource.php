<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Artikel';

    protected static ?string $pluralLabel = 'Artikel';

    protected static ?string $label = 'Artikel';

    protected static ?string $slug = 'articles';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konten Utama')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Artikel')
                            ->required()
                            ->minLength(10)
                            ->maxLength(255)
                            ->placeholder('Contoh: 5 Tips Merawat Furniture Kayu Jati agar Awet'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generate saat disimpan'),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan / Excerpt')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Ringkasan singkat artikel 1-3 kalimat untuk SEO & listing'),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Artikel')
                            ->required()
                            ->minLength(200)
                            ->maxLength(50000)
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'heading',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Cover / Hero')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->imageResizeTargetWidth(2400)
                            ->imageResizeMode('cover')
                            ->panelLayout('integrated')
                            ->directory('articles')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpan(2)
                            ->maxSize(8192)
                            ->imagePreviewHeight('480px'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Jadwal Terbit')
                            ->required()
                            ->default(now())
                            ->seconds(false)
                            ->placeholder('Kapan artikel diterbitkan'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Terbitkan')
                            ->default(true)
                            ->helperText('Jika mati, artikel jadi draf & tidak muncul di halaman publik'),
                        Forms\Components\Toggle::make('is_hero')
                            ->label('Hero Banner Utama')
                            ->default(false)
                            ->helperText('Max. 3 artikel hero aktif. Jika melebihi 3, sistem otomatis menonaktifkan artikel hero terlama.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Cover')
                    ->disk('public')
                    ->square()
                    ->size(72)
                    ->checkFileExistence(false)
                    ->defaultImageUrl(url('https://placehold.co/400x400/fafaf9/78716c?text=Article')),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(60),
                Tables\Columns\IconColumn::make('is_hero')
                    ->label('Hero')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tgl Terbit')
                    ->sortable()
                    ->dateTime('d M Y'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->sortable()
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Terbit'),
                Tables\Filters\TernaryFilter::make('is_hero')
                    ->label('Hero Banner'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->before(function (Article $record, ImageService $imageService) {
                        $imageService->deleteIfExists($record->image);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records, ImageService $imageService) {
                            $records->each(function (Article $article) use ($imageService) {
                                $imageService->deleteIfExists($article->image);
                            });
                        }),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
