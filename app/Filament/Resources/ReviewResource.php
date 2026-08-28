<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Review';

    protected static ?string $pluralLabel = 'Review Pelanggan';

    protected static ?string $label = 'Review';

    protected static ?string $slug = 'reviews';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Review')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Produk')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('product', 'name'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Reviewer')
                            ->required()
                            ->minLength(2)
                            ->maxLength(100)
                            ->placeholder('Contoh: Budi Santoso'),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota / Asal')
                            ->maxLength(100)
                            ->default('Indonesia')
                            ->placeholder('Contoh: Semarang, Jawa Tengah'),
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->required()
                            ->options([
                                1 => '1 Bintang',
                                2 => '2 Bintang',
                                3 => '3 Bintang',
                                4 => '4 Bintang',
                                5 => '5 Bintang',
                            ])
                            ->default(5)
                            ->native(false)
                            ->suffixIcon('heroicon-m-star'),
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Disetujui & Tampilkan')
                            ->default(true)
                            ->required(),
                        Forms\Components\Textarea::make('review')
                            ->label('Teks Ulasan')
                            ->required()
                            ->rows(5)
                            ->minLength(10)
                            ->maxLength(2000)
                            ->columnSpanFull()
                            ->placeholder('Tulis review pelanggan di sini...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->limit(40)
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => "{$state} Bintang")
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('review')
                    ->label('Ulasan')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Status Persetujuan'),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1 Bintang',
                        2 => '2 Bintang',
                        3 => '3 Bintang',
                        4 => '4 Bintang',
                        5 => '5 Bintang',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
