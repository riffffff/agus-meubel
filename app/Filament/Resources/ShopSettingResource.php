<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopSettingResource\Pages\ListShopSettings;
use App\Filament\Resources\ShopSettingResource\Pages\CreateShopSetting;
use App\Filament\Resources\ShopSettingResource\Pages\EditShopSetting;
use App\Models\ShopSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShopSettingResource extends Resource
{
    protected static ?string $model = ShopSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Toko';

    protected static ?string $pluralLabel = 'Pengaturan Toko';

    protected static ?string $label = 'Pengaturan Toko';

    protected static ?string $slug = 'shop-settings';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Toko')
                    ->schema([
                        Forms\Components\TextInput::make('shop_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Agus Mebel Jepara'),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Toko')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Contoh: Jepara, Jawa Tengah, Indonesia'),
                    ])->columns(1),

                Forms\Components\Section::make('Kontak WhatsApp')
                    ->schema([
                        Forms\Components\TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('Contoh: 6281234567890 atau 081234567890')
                            ->helperText('Awali dengan 62 atau 0. Akan dikonversi otomatis ke format internasional.'),
                        Forms\Components\Textarea::make('whatsapp_template')
                            ->label('Template Pesan WhatsApp')
                            ->rows(3)
                            ->required()
                            ->maxLength(2000)
                            ->placeholder('Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah tersedia?')
                            ->helperText('Gunakan placeholder {product_name} dan {product_price} untuk nama & harga produk otomatis.'),
                    ])->columns(1),

                Forms\Components\Section::make('Jam Operasional & Pengiriman')
                    ->schema([
                        Forms\Components\TextInput::make('operating_hours')
                            ->label('Jam Operasional')
                            ->maxLength(255)
                            ->placeholder('Contoh: Senin - Sabtu: 08:00 - 17:00'),
                        Forms\Components\TagsInput::make('shipping_areas')
                            ->label('Area Pengiriman')
                            ->placeholder('Contoh: Seluruh Indonesia, Jawa Tengah, Luar Jawa')
                            ->separator(',')
                            ->suggestions([
                                'Seluruh Indonesia',
                                'Jawa Tengah',
                                'Jawa Barat',
                                'Jawa Timur',
                                'Jabodetabek',
                                'Luar Jawa',
                                'Pulau Sumatera',
                                'Pulau Kalimantan',
                                'Pulau Sulawesi',
                            ]),
                        Forms\Components\TextInput::make('shipping_estimate_days')
                            ->label('Estimasi Waktu Pengiriman')
                            ->maxLength(255)
                            ->placeholder('Contoh: 7 - 14 hari kerja'),
                    ])->columns(2),

                Forms\Components\Section::make('Hero Banner Utama')
                    ->schema([
                        Forms\Components\TextInput::make('hero_banner_text_1')
                            ->label('Teks Baris 1')
                            ->maxLength(255)
                            ->placeholder('Contoh: Furniture Kayu Jati Premium'),
                        Forms\Components\TextInput::make('hero_banner_text_2')
                            ->label('Teks Baris 2')
                            ->maxLength(255)
                            ->placeholder('Contoh: Kualitas Terbaik Langsung dari Pengrajin Jepara'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shop_name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('whatsapp_number')
                    ->label('Nomor WA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operating_hours')
                    ->label('Jam Buka')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit Pengaturan'),
            ])
            ->bulkActions([
                //
            ])
            ->headerActions([
                //
            ])
            ->defaultSort('updated_at', 'desc');
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
            'index' => ListShopSettings::route('/'),
            'create' => CreateShopSetting::route('/create'),
            'edit' => EditShopSetting::route('/{record}/edit'),
        ];
    }
}
