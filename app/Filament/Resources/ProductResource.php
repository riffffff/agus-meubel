<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $pluralLabel = 'Produk';

    protected static ?string $label = 'Produk';

    protected static ?string $slug = 'products';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
                            ->minLength(3)
                            ->placeholder('Contoh: Kursi Tamu Minimalis Kayu Jati'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generate saat disimpan'),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->searchable()
                            ->options([
                                'kursi_tamu' => 'Kursi Tamu / Sofa',
                                'meja_makan' => 'Set Meja Makan',
                                'tempat_tidur' => 'Tempat Tidur / Dipan',
                                'lemari' => 'Lemari Pakaian',
                                'meja_kerja' => 'Meja Kerja / Belajar',
                                'bufet' => 'Bufet / Hiasan Dinding',
                                'rak' => 'Rak Buku / Display',
                                'aksesoris' => 'Aksesoris & Pajangan',
                                'lainnya' => 'Lainnya',
                            ])
                            ->nullable()
                            ->default('lainnya')
                            ->placeholder('Pilih kategori (opsional)'),
                        Forms\Components\TextInput::make('price')
                            ->label('Harga (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->maxValue(9999999999)
                            ->step(1)
                            ->placeholder('0')
                            ->formatStateUsing(function ($state) {
                                if ($state === null || $state === '') {
                                    return '';
                                }
                                return number_format((float) $state, 0, ',', '.');
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (is_string($state)) {
                                    $state = preg_replace('/[^0-9]/', '', $state);
                                }
                                return (int) $state;
                            }),
                        Forms\Components\Select::make('stock_status')
                            ->label('Status Ketersediaan')
                            ->required()
                            ->options([
                                'available' => 'Tersedia',
                                'preorder' => 'Pre Order',
                                'out_of_stock' => 'Kosong',
                            ])
                            ->default('available'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Tampilkan di Katalog')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Gambar Produk')
                    ->description('Unggah minimal 1 gambar. Urutan gambar pertama = Gambar Utama / Cover. Anda bisa drag untuk mengurutkan. Hapus gambar lama jika ingin diganti.')
                    ->schema([
                        Forms\Components\Repeater::make('existing_images')
                            ->hiddenOn('create')
                            ->label('Gambar Saat Ini (Klik Hapus untuk menghilangkan)')
                            ->helperText('Centang "Jadikan Gambar Utama" pada 1 gambar saja. Drag item untuk mengubah urutan tampilan di katalog.')
                            ->relationship('images')
                            ->orderColumn('sort_order')
                            ->addable(false)
                            ->deletable(true)
                            ->reorderableWithDragAndDrop(true)
                            ->itemLabel(fn (array $state): string => 'Gambar')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\Hidden::make('url'),
                                Forms\Components\Placeholder::make('preview')
                                    ->label('Pratinjau')
                                    ->columnSpanFull()
                                    ->content(function (?ProductImage $record) {
                                        $url = $record?->url;
                                        $imageUrl = $url ? asset('storage/' . $url) : null;

                                        if (!$imageUrl) {
                                            return new HtmlString(
                                                '<div class="py-8 text-center text-stone-400 text-sm w-full">' .
                                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">' .
                                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>' .
                                                '</svg><span class="text-stone-500 font-medium">(tidak ada gambar)</span></div>'
                                            );
                                        }

                                        $escUrl = htmlspecialchars($imageUrl, ENT_QUOTES);
                                        $escPath = htmlspecialchars($url, ENT_QUOTES);

                                        return new HtmlString(
                                            '<div class="w-full flex justify-center py-3">' .
                                            '<img src="' . $escUrl . '" alt="Product Image" ' .
                                            'style="max-height: 280px; max-width: 100%; border-radius: 0.75rem; object-fit: contain; border: 1px solid #e7e5e4; padding: 0.375rem; background: #fff; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">' .
                                            '<div style="display:none; padding:0.5rem 0; font-size:0.7rem; color:#a8a29e; text-align:center; word-break: break-all;">' .
                                            'Image path: <code>' . $escPath . '</code></div>' .
                                            '</div>'
                                        );
                                    }),
                                Forms\Components\Toggle::make('is_primary')
                                    ->label('Jadikan Gambar Utama / Cover')
                                    ->helperText('Sistem otomatis memastikan hanya 1 gambar utama yang aktif.')
                                    ->columnSpanFull(),
                            ]),
                        FileUpload::make('new_images')
                            ->label('Tambah / Unggah Gambar Baru')
                            ->helperText('Gambar akan otomatis di-konversi WebP & dioptimalkan ukurannya setelah disimpan. Tambahkan dulu gambar baru lalu klik Save Changes.')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->dehydrated(false)
                            ->image()
                            ->panelLayout('grid')
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public')
                            ->minFiles(0)
                            ->maxFiles(10)
                            ->maxSize(10240),
                    ]),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->maxLength(500)
                            ->required()
                            ->placeholder('Ringkasan singkat 1-2 kalimat untuk listing katalog'),
                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi Lengkap')
                            ->required()
                            ->minLength(50)
                            ->maxLength(20000)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'heading',
                                'link',
                                'h2',
                                'h3',
                                'blockquote',
                                'redo',
                                'undo',
                            ])
                            ->placeholder('Deskripsi detail produk, material, keunggulan, dll.'),
                    ])->columns(1),

                Forms\Components\Section::make('Spesifikasi')
                    ->schema([
                        Repeater::make('dimensions')
                            ->label('Dimensi / Ukuran')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Bagian')
                                    ->required()
                                    ->placeholder('Contoh: Panjang'),
                                Forms\Components\TextInput::make('value')
                                    ->label('Nilai')
                                    ->required()
                                    ->placeholder('Contoh: 200 cm'),
                            ])
                            ->default([
                                ['label' => 'Panjang', 'value' => ''],
                                ['label' => 'Lebar', 'value' => ''],
                                ['label' => 'Tinggi', 'value' => ''],
                            ])
                            ->columnSpan(2)
                            ->collapsible()
                            ->cloneable(),
                        Repeater::make('materials')
                            ->label('Material Bahan')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Bahan')
                                    ->required()
                                    ->placeholder('Contoh: Kayu Jati Solid Perhutani'),
                            ])
                            ->default([
                                ['name' => 'Kayu Jati Solid'],
                            ])
                            ->columnSpan(1)
                            ->collapsible()
                            ->cloneable(),
                        Repeater::make('finishes')
                            ->label('Pilihan Finishing / Warna')
                            ->schema([
                                Forms\Components\TextInput::make('option')
                                    ->label('Finishing')
                                    ->required()
                                    ->placeholder('Contoh: Natural Brown Glossy'),
                            ])
                            ->default([
                                ['option' => 'Natural Brown'],
                            ])
                            ->columnSpan(1)
                            ->collapsible()
                            ->cloneable(),
                        Forms\Components\TextInput::make('weight_kg')
                            ->label('Berat (kg)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10000)
                            ->placeholder('Contoh: 45.5')
                            ->suffix('kg'),
                        Forms\Components\Toggle::make('assembly_required')
                            ->label('Perlu Rakitan Mandiri')
                            ->default(false),
                        Forms\Components\TextInput::make('warranty_months')
                            ->label('Lama Garansi')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(600)
                            ->suffix('bulan')
                            ->default(12),
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tag / Kata Kunci')
                            ->separator(',')
                            ->suggestions([
                                'jati', 'minimalis', 'klasik', 'modern', 'skandinavia',
                                'meja', 'kursi', 'sofa', 'lemari', 'dipan', 'bufet',
                            ])
                            ->columnSpan(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryImage.url')
                    ->label('Cover')
                    ->square()
                    ->size(72)
                    ->disk('public')
                    ->checkFileExistence(false)
                    ->defaultImageUrl(url('https://placehold.co/400x400/fafaf9/78716c?text=No+Image')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(function (string $state): string {
                        $labels = [
                            'kursi_tamu' => 'Kursi Tamu',
                            'meja_makan' => 'Meja Makan',
                            'tempat_tidur' => 'Tempat Tidur',
                            'lemari' => 'Lemari',
                            'meja_kerja' => 'Meja Kerja',
                            'bufet' => 'Bufet',
                            'rak' => 'Rak',
                            'aksesoris' => 'Aksesoris',
                            'lainnya' => 'Lainnya',
                        ];
                        return $labels[$state] ?? ucwords(str_replace('_', ' ', $state));
                    })
                    ->colors([
                        'info' => 'kursi_tamu',
                        'success' => 'meja_makan',
                        'warning' => 'tempat_tidur',
                        'primary' => 'lemari',
                        'secondary' => 'lainnya',
                    ]),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->numeric(0, ',', '.')
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('stock_status')
                    ->label('Stok')
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->options([
                        'available' => 'Tersedia',
                        'preorder' => 'Pre Order',
                        'out_of_stock' => 'Kosong',
                    ]),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Dipublikasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('images_count')
                    ->label('Gambar')
                    ->counts('images')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => "{$state} gambar"),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'kursi_tamu' => 'Kursi Tamu / Sofa',
                        'meja_makan' => 'Set Meja Makan',
                        'tempat_tidur' => 'Tempat Tidur / Dipan',
                        'lemari' => 'Lemari Pakaian',
                        'meja_kerja' => 'Meja Kerja / Belajar',
                        'bufet' => 'Bufet / Hiasan Dinding',
                        'rak' => 'Rak Buku / Display',
                        'aksesoris' => 'Aksesoris & Pajangan',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Status Stok')
                    ->options([
                        'available' => 'Tersedia',
                        'preorder' => 'Pre Order',
                        'out_of_stock' => 'Kosong',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->before(function (Product $record, ImageService $imageService) {
                        foreach ($record->images as $image) {
                            $imageService->deleteIfExists($image->url);
                            ProductImage::destroy($image->id);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records, ImageService $imageService) {
                            $records->each(function (Product $product) use ($imageService) {
                                foreach ($product->images as $image) {
                                    $imageService->deleteIfExists($image->url);
                                    ProductImage::destroy($image->id);
                                }
                            });
                        }),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
