<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <x-slot name="heading">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <x-heroicon-s-chart-pie style="width:14px;height:14px;color:#fff;"/>
                </div>
                <span style="font-weight:700;font-size:0.925rem;letter-spacing:-0.02em;">Status Stok Produk</span>
            </div>
        </x-slot>

        {{-- Donut SVG (pure CSS/SVG, no JS dependency) --}}
        @php
            $segments = [
                ['value'=>$available,  'color'=>'#10b981','label'=>'Ready Stock', 'bg'=>'rgba(16,185,129,0.1)','text'=>'#059669'],
                ['value'=>$preorder,   'color'=>'#f59e0b','label'=>'Pre-order',   'bg'=>'rgba(245,158,11,0.1)','text'=>'#d97706'],
                ['value'=>$outOfStock, 'color'=>'#f43f5e','label'=>'Habis',       'bg'=>'rgba(244,63,94,0.1)', 'text'=>'#e11d48'],
                ['value'=>$draft,      'color'=>'#94a3b8','label'=>'Draft',       'bg'=>'rgba(148,163,184,0.1)','text'=>'#64748b'],
            ];
            $total = max($total, 1);

            // SVG donut parameters
            $cx = 80; $cy = 80; $r = 62; $innerR = 44;
            $circumference = 2 * M_PI * $r;
            $offset = 0;
            $strokeWidth = $r - $innerR;
        @endphp

        <div style="display:flex;align-items:center;gap:20px;padding:8px 0;">
            {{-- Donut chart --}}
            <div style="position:relative;flex-shrink:0;width:160px;height:160px;">
                <svg width="160" height="160" style="transform:rotate(-90deg);overflow:visible;">
                    {{-- Background track --}}
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                        fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="{{ $strokeWidth }}"/>

                    {{-- Segments --}}
                    @foreach($segments as $seg)
                        @php
                            $pct    = $total > 0 ? ($seg['value'] / $total) : 0;
                            $dash   = $pct * $circumference;
                            $gap    = $circumference - $dash;
                        @endphp
                        @if($seg['value'] > 0)
                            <circle
                                cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                                fill="none"
                                stroke="{{ $seg['color'] }}"
                                stroke-width="{{ $strokeWidth }}"
                                stroke-dasharray="{{ round($dash - 3, 2) }} {{ round($gap + 3, 2) }}"
                                stroke-dashoffset="{{ round(-$offset * $circumference / $total, 2) }}"
                                stroke-linecap="round"
                                style="transition:stroke-dasharray 0.5s ease;"/>
                            @php $offset += $seg['value']; @endphp
                        @endif
                    @endforeach
                </svg>

                {{-- Center label --}}
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:1.6rem;font-weight:800;letter-spacing:-0.04em;line-height:1;">{{ $total }}</span>
                    <span style="font-size:0.62rem;color:#94a3b8;font-weight:600;margin-top:2px;text-transform:uppercase;letter-spacing:0.06em;">Produk</span>
                </div>
            </div>

            {{-- Legend + stats --}}
            <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                @foreach($segments as $seg)
                    @php $pct = $total > 0 ? round($seg['value'] / $total * 100) : 0; @endphp
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <div style="display:flex;align-items:center;gap:7px;">
                                <span style="width:8px;height:8px;border-radius:50%;background:{{ $seg['color'] }};flex-shrink:0;"></span>
                                <span style="font-size:0.75rem;font-weight:600;color:#64748b;">{{ $seg['label'] }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="font-size:0.75rem;font-weight:800;color:{{ $seg['text'] }};">{{ $seg['value'] }}</span>
                                <span style="font-size:0.65rem;color:#cbd5e1;font-weight:500;">{{ $pct }}%</span>
                            </div>
                        </div>
                        {{-- Progress bar --}}
                        <div style="height:4px;border-radius:99px;background:rgba(0,0,0,0.06);overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;border-radius:99px;background:{{ $seg['color'] }};transition:width 0.6s ease;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
