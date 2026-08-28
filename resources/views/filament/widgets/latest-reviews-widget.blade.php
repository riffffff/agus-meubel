<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <x-slot name="heading">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:linear-gradient(135deg,#6366f1 0%,#4f46e5 100%);width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <x-heroicon-s-chat-bubble-left-ellipsis style="width:14px;height:14px;color:#fff;"/>
                </div>
                <span style="font-weight:700;font-size:0.925rem;letter-spacing:-0.02em;">Ulasan Pelanggan Terbaru</span>
            </div>
        </x-slot>

        <x-slot name="headerEnd">
            <a href="{{ route('filament.admin.resources.reviews.index') }}"
               style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;font-weight:600;color:#6366f1;text-decoration:none;opacity:0.9;"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                Lihat semua
                <x-heroicon-m-arrow-right style="width:12px;height:12px;"/>
            </a>
        </x-slot>

        @php
            $avatarGradients = [
                ['#f59e0b','#d97706'],['#10b981','#059669'],
                ['#6366f1','#4f46e5'],['#f43f5e','#e11d48'],
                ['#8b5cf6','#7c3aed'],
            ];
        @endphp

        <div style="display:flex;flex-direction:column;gap:10px;">
            @forelse($reviews as $i => $review)
                @php
                    $words   = explode(' ', $review->name);
                    $initials= strtoupper(substr($words[0],0,1)) . (isset($words[1]) ? strtoupper(substr($words[1],0,1)) : '');
                    $grad    = $avatarGradients[$i % count($avatarGradients)];
                    $stars   = (int) $review->rating;
                    $editUrl = \App\Filament\Resources\ReviewResource::getUrl('edit',['record'=>$review]);
                @endphp

                <div class="aw-review-card"
                     style="padding:14px;border:1px solid rgba(0,0,0,0.07);border-radius:12px;background:rgba(255,255,255,0.5);">

                    {{-- Top row: avatar + name + rating + status --}}
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        {{-- Avatar --}}
                        <div style="flex-shrink:0;width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,{{ $grad[0] }},{{ $grad[1] }});display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;color:#fff;letter-spacing:-0.03em;">
                            {{ $initials }}
                        </div>

                        {{-- Name + product + stars --}}
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
                                <span style="font-size:0.85rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $review->name }}</span>
                                {{-- Status badge --}}
                                @if($review->is_approved)
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:20px;background:rgba(16,185,129,0.12);font-size:0.65rem;font-weight:700;color:#059669;flex-shrink:0;white-space:nowrap;">
                                        <x-heroicon-m-check-circle style="width:10px;height:10px;"/>Disetujui
                                    </span>
                                @else
                                    <span class="aw-pending-badge" style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:20px;background:rgba(245,158,11,0.15);font-size:0.65rem;font-weight:700;color:#d97706;flex-shrink:0;white-space:nowrap;">
                                        <x-heroicon-m-clock style="width:10px;height:10px;"/>Moderasi
                                    </span>
                                @endif
                            </div>

                            {{-- Stars --}}
                            <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
                                <span style="font-size:0.72rem;letter-spacing:1px;color:#f59e0b;">
                                    {{ str_repeat('★', $stars) }}<span style="color:#e2e8f0;">{{ str_repeat('★', 5-$stars) }}</span>
                                </span>
                                @if($review->city)
                                    <span style="font-size:0.68rem;color:#94a3b8;">• {{ $review->city }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Review text --}}
                    <p style="font-size:0.78rem;color:#64748b;margin:10px 0 0;line-height:1.5;padding-left:48px;">
                        &ldquo;{{ \Illuminate\Support\Str::limit($review->review, 80) }}&rdquo;
                    </p>

                    {{-- Product tag + actions --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px;padding-left:48px;">
                        @if($review->product)
                            <span style="font-size:0.68rem;color:#94a3b8;display:flex;align-items:center;gap:4px;">
                                <x-heroicon-m-cube style="width:10px;height:10px;"/>
                                {{ \Illuminate\Support\Str::limit($review->product->name, 25) }}
                            </span>
                        @else
                            <span></span>
                        @endif

                        <div style="display:flex;align-items:center;gap:6px;">
                            @if(!$review->is_approved)
                                <button
                                    wire:click="approveReview({{ $review->id }})"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:0.7rem;font-weight:700;border:none;cursor:pointer;transition:opacity 0.15s;"
                                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                    <x-heroicon-m-check style="width:10px;height:10px;"/>
                                    Setujui
                                </button>
                            @endif
                            <a href="{{ $editUrl }}"
                               style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:7px;background:rgba(99,102,241,0.1);text-decoration:none;transition:background 0.15s;"
                               onmouseover="this.style.background='rgba(99,102,241,0.2)'"
                               onmouseout="this.style.background='rgba(99,102,241,0.1)'">
                                <x-heroicon-m-pencil-square style="width:12px;height:12px;color:#6366f1;"/>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:2rem;text-align:center;color:#94a3b8;">
                    <x-heroicon-o-star style="width:32px;height:32px;margin:0 auto 8px;"/>
                    <p style="font-size:0.875rem;margin:0;">Belum ada ulasan.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
