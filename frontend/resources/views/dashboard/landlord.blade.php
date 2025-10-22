@extends('layouts.owner')


@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Property Listings -->
        <h2 class="text-xl font-semibold mb-6 text-foreground">Trọ của tôi</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach($buildings as $building)
                <a href="{{ url('/buildings/' . $building['id'] . '/rooms') }}"
                    class="overflow-hidden rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-lg transition-shadow">
                    <div class="relative">
                        <img src="{{ $building['imageUrls'][0] ?? '/placeholder.svg' }}" alt="{{ $building['buildingName'] }}"
                            class="w-full h-48 object-cover" />


                        <div class="absolute bottom-2 left-2 flex gap-2 text-white text-xs">
                            <div class="bg-black/60 px-2 py-1 rounded flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                                    <circle cx="12" cy="13" r="3" />
                                </svg>
                                <span>{{ count($building['imageUrls'] ?? []) }} ảnh</span>
                            </div>

                            @if(isset($building['bedrooms']) && $building['bedrooms'] > 0)
                                <div class="bg-black/60 px-2 py-1 rounded flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 4v16" />
                                        <path d="M2 8h18a2 2 0 0 1 2 2v10" />
                                        <path d="M2 17h20" />
                                        <path d="M6 8v9" />
                                    </svg>
                                    <span>{{ $building['bedrooms'] }} PN</span>
                                </div>
                            @endif

                            @if(isset($building['bathrooms']) && $building['bathrooms'] > 0)
                                <div class="bg-black/60 px-2 py-1 rounded flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5" />
                                        <line x1="10" x2="8" y1="5" y2="7" />
                                        <line x1="2" x2="22" y1="12" y2="12" />
                                        <line x1="7" x2="7" y1="19" y2="21" />
                                        <line x1="17" x2="17" y1="19" y2="21" />
                                    </svg>
                                    <span>{{ $building['bathrooms'] }} WC</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-3">
                        <h3 class="text-sm font-medium text-foreground line-clamp-2 mb-2 min-h-[40px]">
                            {{ $building['buildingName'] }}
                        </h3>

                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-lg font-bold text-[#E03C31]">{{ number_format($building['price'] ?? 0) }} đ</span>
                            <span class="text-xs text-muted-foreground">{{ $building['totalRooms'] }} phòng</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <span class="line-clamp-1">{{ $building['address'] }}</span>
                            </div>
                            <button class="p-1 hover:bg-muted rounded" aria-label="More options">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="text-muted-foreground">
                                    <circle cx="12" cy="12" r="1" />
                                    <circle cx="12" cy="5" r="1" />
                                    <circle cx="12" cy="19" r="1" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center">
            <button
                class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 px-8">
                Xem thêm
            </button>
        </div>
    </div>
@endsection