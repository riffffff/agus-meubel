<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <x-slot name="heading">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <x-heroicon-s-fire style="width:14px;height:14px;color:#fff;"/>
                </div>
                <span style="font-weight:700;font-size:0.925rem;letter-spacing:-0.02em;">Produk Terpopuler</span>
            </div>
        </x-slot>

        <x-slot name="headerEnd">
            <a href="{{ route('filament.admin.resources.products.index') }}"
               style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;font-weight:600;color:#d97706;text-decoration:none;opacity:0.9;"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                Lihat semua
                <x-heroicon-m-arrow-right style="width:12px;height:12px;"/>
            </a>
        </x-slot>

        {{-- Column Labels --}}
        <div style="display:flex;align-items:center;gap:14px;padding:4px 12px 6px;border-bottom:1px solid rgba(0,0,0,0.06);margin-bottom:4px;">
            <div style="width:24px;flex-shrink:0;"></div>
            <div style="width:44px;flex-shrink:0;"></div>
            <div style="flex:1;font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;">Produk</div>
            <div style="width:110px;flex-shrink:0;font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;text-align:right;">Harga</div>
            <div style="width:80px;flex-shrink:0;font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;text-align:center;">Stok</div>
            <div style="width:56px;flex-shrink:0;font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;text-align:right;">Views</div>
            <div style="width:30px;flex-shrink:0;"></div>
        </div>

        {{-- Rows --}}
        @php
            $catLabels = [
                'kursi_tamu'=>'Kursi Tamu','meja_makan'=>'Meja Makan',
                'tempat_tidur'=>'Tempat Tidur','lemari'=>'Lemari',
                'meja_kerja'=>'Meja Kerja','bufet'=>'Bufet',
                'rak'=>'Rak','aksesoris'=>'Aksesoris',
            ];
            $rankColors = ['#f59e0b','#94a3b8','#b45309'];
            $stockInfo = [
                'available' => ['dot'=>'#10b981','label'=>'Ready','bg'=>'rgba(16,185,129,0.12)','color'=>'#059669'],
                'preorder'  => ['dot'=>'#f59e0b','label'=>'Pre-order','bg'=>'rgba(245,158,11,0.12)','color'=>'#d97706'],
                'out_of_stock'=>['dot'=>'#f43f5e','label'=>'Habis','bg'=>'rgba(244,63,94,0.12)','color'=>'#e11d48'],
            ];
        @endphp

        @forelse($products as $i => $product)
            @php
                $cat   = $catLabels[$product->category] ?? ucwords(str_replace('_',' ',$product->category ?? 'Lainnya'));
                $stock = $stockInfo[$product->stock_status] ?? $stockInfo['available'];
                $img   = $product->primaryImage?->url
                    ? asset('storage/'.$product->primaryImage->url)
                    : 'https://placehold.co/200x200/fafaf9/a8a29e?text='.urlencode(substr($product->name,0,1));
                $editUrl = \App\Filament\Resources\ProductResource::getUrl('edit',['record'=>$product]);
                $isLast  = $i === count($products) - 1;
            @endphp

            <div class="aw-product-row"
                 style="display:flex;align-items:center;gap:14px;padding:9px 12px;{{ !$isLast ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : '' }}">

                {{-- Rank --}}
                <div style="width:24px;flex-shrink:0;text-align:center;">
                    @if($i < 3)
                        <span style="font-size:0.8rem;font-weight:800;color:{{ $rankColors[$i] }};">#{{ $i+1 }}</span>
                    @else
                        <span style="font-size:0.72rem;color:#cbd5e1;font-weight:600;">{{ $i+1 }}</span>
                    @endif
                </div>

                {{-- Avatar --}}
                <div style="flex-shrink:0;width:44px;height:44px;border-radius:10px;overflow:hidden;border:1.5px solid rgba(245,158,11,0.25);background:#fafaf9;">
                    <img src="{{ $img }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>

                {{-- Name + Category --}}
                <div style="flex:1;min-width:0;">
                    <p style="font-size:0.855rem;font-weight:700;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->name }}</p>
                    @if($cat && $cat !== 'Lainnya')
                        <p style="font-size:0.7rem;color:#94a3b8;margin:0;margin-top:1px;">{{ $cat }}</p>
                    @endif
                </div>

                {{-- Price --}}
                <div style="width:110px;flex-shrink:0;text-align:right;">
                    <span style="font-size:0.83rem;font-weight:700;color:#10b981;white-space:nowrap;">
                        Rp {{ number_format((float)$product->price, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Stock --}}
                <div style="width:80px;flex-shrink:0;text-align:center;">
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;background:{{ $stock['bg'] }};font-size:0.68rem;font-weight:700;color:{{ $stock['color'] }};white-space:nowrap;">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $stock['dot'] }};flex-shrink:0;"></span>
                        {{ $stock['label'] }}
                    </span>
                </div>

                {{-- Views --}}
                <div style="width:56px;flex-shrink:0;display:flex;align-items:center;justify-content:flex-end;gap:4px;">
                    <x-heroicon-m-eye style="width:12px;height:12px;color:#94a3b8;flex-shrink:0;"/>
                    <span style="font-size:0.75rem;font-weight:600;color:#64748b;">{{ number_format($product->views_count ?? 0) }}</span>
                </div>

                {{-- Edit --}}
                <a href="{{ $editUrl }}"
                   style="flex-shrink:0;width:30px;height:30px;border-radius:8px;background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;text-decoration:none;transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(245,158,11,0.2)'"
                   onmouseout="this.style.background='rgba(245,158,11,0.1)'">
                    <x-heroicon-m-pencil-square style="width:13px;height:13px;color:#d97706;"/>
                </a>
            </div>
        @empty
            <div style="padding:2.5rem;text-align:center;color:#94a3b8;">
                <x-heroicon-o-cube style="width:36px;height:36px;margin:0 auto 8px;"/>
                <p style="font-size:0.875rem;margin:0;">Belum ada produk.</p>
            </div>
        @endforelse
    </x-filament::section>
</x-filament-widgets::widget>
