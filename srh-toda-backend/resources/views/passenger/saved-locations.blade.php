<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Saved Places') }}"></x-page-header>
    </x-slot>

    @php
        $schoolLocation = $savedLocations->firstWhere('type', 'school');
        $workLocation = $savedLocations->firstWhere('type', 'work');
        $otherLocations = $savedLocations->reject(fn($loc) => in_array($loc->type, ['school', 'work']));
    @endphp

    <div class="py-6 sm:py-8 bg-slate-50/60">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5 pb-24 sm:pb-12">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{-- SECTION 1: DAILY COMMUTE SHORTCUTS (SCHOOL & WORK) --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-500">Daily Commute Shortcuts</h2>
                    </div>
                    <span class="text-[11px] font-bold text-slate-400">1-Tap Fast Booking</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- SCHOOL CARD --}}
                    @if($schoolLocation)
                        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0 shadow-2xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-extrabold text-slate-900 text-base truncate">{{ $schoolLocation->name }}</h3>
                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 shrink-0">School</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium line-clamp-1 mt-0.5" title="{{ $schoolLocation->address }}">{{ $schoolLocation->address }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-2">
                                <a href="{{ route('dashboard') }}?dest_lat={{ $schoolLocation->latitude }}&dest_lng={{ $schoolLocation->longitude }}&dest_name={{ urlencode($schoolLocation->name) }}&dest_addr={{ urlencode($schoolLocation->address) }}" class="flex-1 py-2.5 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-black uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-sm shadow-blue-500/20 transition-all text-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Book Ride to School</span>
                                </a>
                                <button type="button" onclick="openEditSavedLocationModal({{ json_encode($schoolLocation) }})" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-colors cursor-pointer" title="Edit School">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('saved-locations.destroy', $schoolLocation) }}" method="POST" onsubmit="return confirm('Delete School address?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition-colors cursor-pointer" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <button type="button" onclick="openAddSavedLocationModal('school', 'School', 'Campus / High School / College')" class="bg-white hover:bg-amber-50/50 rounded-3xl p-5 border-2 border-dashed border-slate-200 hover:border-amber-300 shadow-xs transition-all flex items-center gap-3.5 text-left group cursor-pointer">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-black text-slate-800 group-hover:text-amber-600 block transition-colors">+ Set School Address</span>
                                <span class="text-[11px] font-semibold text-slate-400">Save for 1-tap rides to campus</span>
                            </div>
                        </button>
                    @endif

                    {{-- WORK CARD --}}
                    @if($workLocation)
                        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 shadow-2xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-extrabold text-slate-900 text-base truncate">{{ $workLocation->name }}</h3>
                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0">Work</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium line-clamp-1 mt-0.5" title="{{ $workLocation->address }}">{{ $workLocation->address }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-2">
                                <a href="{{ route('dashboard') }}?dest_lat={{ $workLocation->latitude }}&dest_lng={{ $workLocation->longitude }}&dest_name={{ urlencode($workLocation->name) }}&dest_addr={{ urlencode($workLocation->address) }}" class="flex-1 py-2.5 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-black uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-sm shadow-blue-500/20 transition-all text-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Book Ride to Work</span>
                                </a>
                                <button type="button" onclick="openEditSavedLocationModal({{ json_encode($workLocation) }})" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-colors cursor-pointer" title="Edit Work">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('saved-locations.destroy', $workLocation) }}" method="POST" onsubmit="return confirm('Delete Work address?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition-colors cursor-pointer" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <button type="button" onclick="openAddSavedLocationModal('work', 'Work', 'Workplace Address')" class="bg-white hover:bg-indigo-50/50 rounded-3xl p-5 border-2 border-dashed border-slate-200 hover:border-indigo-300 shadow-xs transition-all flex items-center gap-3.5 text-left group cursor-pointer">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-black text-slate-800 group-hover:text-indigo-600 block transition-colors">+ Set Work Address</span>
                                <span class="text-[11px] font-semibold text-slate-400">Office, shop, or commercial workplace</span>
                            </div>
                        </button>
                    @endif
                </div>

                {{-- Prominent Add Place Button directly below Set Work/School Address --}}
                <div class="pt-1">
                    <button type="button" onclick="openAddSavedLocationModal()" class="w-full py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs sm:text-sm font-black uppercase tracking-wider flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Add New Saved Place</span>
                    </button>
                </div>
            </div>

            {{-- SECTION 2: SAVED DESTINATIONS (Only shown when custom places exist) --}}
            @if($otherLocations->isNotEmpty())
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-4 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-900 tracking-tight">Saved Destinations</h3>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black" id="places-count-badge">
                                        {{ $otherLocations->count() }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 font-semibold">Your custom spots, markets & terminals</p>
                            </div>
                        </div>
                    </div>

                    @if($otherLocations->count() > 3)
                        {{-- Sleek Compact Search --}}
                        <div class="relative">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="saved-search" autocomplete="off" placeholder="Filter saved places by name or address…"
                                   class="w-full pl-9 pr-4 py-2.5 text-xs font-bold text-slate-900 border border-slate-200 rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                    @endif

                    <div id="saved-places-list" class="divide-y divide-slate-100">
                        @foreach($otherLocations as $loc)
                            @php
                                $type = $loc->type;
                                $tagLabel = match($type) {
                                    'shopping' => 'Market',
                                    'favorite' => 'Favorite',
                                    default => 'Custom',
                                };
                                $badgeClass = match($type) {
                                    'shopping' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'favorite' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                $iconBg = match($type) {
                                    'shopping' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    'favorite' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <div class="saved-place-item content-auto py-3.5 sm:py-4 flex items-center justify-between gap-3 group transition" data-search="{{ strtolower($loc->name . ' ' . $loc->address . ' ' . $tagLabel) }}">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-2xl {{ $iconBg }} border flex items-center justify-center shrink-0 shadow-2xs">
                                        @if($type === 'shopping')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        @elseif($type === 'favorite')
                                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-extrabold text-slate-900 text-sm truncate">{{ $loc->name }}</h4>
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $badgeClass }} border shrink-0">
                                                {{ $tagLabel }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5" title="{{ $loc->address }}">{{ $loc->address }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('dashboard') }}?dest_lat={{ $loc->latitude }}&dest_lng={{ $loc->longitude }}&dest_name={{ urlencode($loc->name . ' (' . $loc->address . ')') }}" class="py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shadow-sm shadow-blue-500/20 transition-all text-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Ride</span>
                                    </a>
                                    <button type="button" onclick="openEditSavedLocationModal({{ json_encode($loc) }})" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-colors cursor-pointer" title="Edit Place">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('saved-locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Delete \'{{ addslashes($loc->name) }}\' from your saved places?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition-colors cursor-pointer" title="Delete Place">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="saved-search-no-results" class="hidden py-8 text-center">
                        <p class="text-xs text-slate-400 font-bold">No saved destinations matched your search.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ADD / EDIT SAVED LOCATION MODAL (Clean, Consistent & Zero Emojis) --}}
    <div id="saved-location-modal" class="hidden fixed inset-0 z-50 flex items-start justify-center p-3 sm:p-4 pt-3 sm:pt-6 pb-20 bg-slate-900/60 backdrop-blur-xs overflow-y-auto" onclick="if(event.target===this) closeSavedLocationModal()">
        <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl border border-slate-100 relative my-auto animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3.5">
                <div class="flex items-center gap-2.5">
                    <div id="modal-category-icon" class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modal-title" class="text-base font-black text-slate-900">Add Saved Place</h3>
                        <p class="text-xs text-slate-500 font-semibold">Pin an exact spot in Santa Rosa or Nueva Ecija</p>
                    </div>
                </div>
                <button type="button" onclick="closeSavedLocationModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-800 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="saved-location-form" method="POST" action="{{ route('saved-locations.store') }}" class="space-y-3.5">
                @csrf
                <input type="hidden" id="form-method-input" name="_method" value="POST">
                <input type="hidden" id="loc-lat-input" name="latitude" value="15.427800">
                <input type="hidden" id="loc-lng-input" name="longitude" value="120.924500">

                {{-- Category Selector (Clean SVG buttons, Zero Emojis, Clear Visual Toggle) --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Select Category</label>
                    <div style="display: flex !important; flex-wrap: wrap !important; justify-content: center !important; gap: 8px !important;">
                        <label class="category-radio cursor-pointer block" style="flex: 1 1 calc(33.333% - 10px); min-width: 80px; max-width: 130px;">
                            <input type="radio" name="type" value="school" class="hidden peer" onchange="onCategoryChanged('school')">
                            <div class="cat-card-box p-2 sm:p-2.5 rounded-2xl border-2 text-center transition-all flex flex-col items-center justify-center gap-1 h-full cursor-pointer" data-cat="school">
                                <svg class="w-4 h-4 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                <span class="text-[10px] font-black uppercase whitespace-nowrap transition-colors">School</span>
                            </div>
                        </label>
                        <label class="category-radio cursor-pointer block" style="flex: 1 1 calc(33.333% - 10px); min-width: 80px; max-width: 130px;">
                            <input type="radio" name="type" value="work" class="hidden peer" onchange="onCategoryChanged('work')">
                            <div class="cat-card-box p-2 sm:p-2.5 rounded-2xl border-2 text-center transition-all flex flex-col items-center justify-center gap-1 h-full cursor-pointer" data-cat="work">
                                <svg class="w-4 h-4 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-[10px] font-black uppercase whitespace-nowrap transition-colors">Work</span>
                            </div>
                        </label>
                        <label class="category-radio cursor-pointer block" style="flex: 1 1 calc(33.333% - 10px); min-width: 80px; max-width: 130px;">
                            <input type="radio" name="type" value="shopping" class="hidden peer" onchange="onCategoryChanged('shopping')">
                            <div class="cat-card-box p-2 sm:p-2.5 rounded-2xl border-2 text-center transition-all flex flex-col items-center justify-center gap-1 h-full cursor-pointer" data-cat="shopping">
                                <svg class="w-4 h-4 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span class="text-[10px] font-black uppercase whitespace-nowrap transition-colors">Market</span>
                            </div>
                        </label>
                        <label class="category-radio cursor-pointer block" style="flex: 1 1 calc(33.333% - 10px); min-width: 90px; max-width: 130px;">
                            <input type="radio" name="type" value="favorite" class="hidden peer" onchange="onCategoryChanged('favorite')">
                            <div class="cat-card-box p-2 sm:p-2.5 rounded-2xl border-2 text-center transition-all flex flex-col items-center justify-center gap-1 h-full cursor-pointer" data-cat="favorite">
                                <svg class="w-4 h-4 shrink-0 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <span class="text-[10px] font-black uppercase whitespace-nowrap transition-colors">Favorite</span>
                            </div>
                        </label>
                        <label class="category-radio cursor-pointer block" style="flex: 1 1 calc(33.333% - 10px); min-width: 90px; max-width: 130px;">
                            <input type="radio" name="type" value="custom" class="hidden peer" checked onchange="onCategoryChanged('custom')">
                            <div class="cat-card-box p-2 sm:p-2.5 rounded-2xl border-2 text-center transition-all flex flex-col items-center justify-center gap-1 h-full cursor-pointer" data-cat="custom">
                                <svg class="w-4 h-4 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[10px] font-black uppercase whitespace-nowrap transition-colors">Custom</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Place Label Name --}}
                <div>
                    <label for="loc-name-input" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Place Name</label>
                    <input type="text" id="loc-name-input" name="name" required placeholder="e.g. NEUST, Office, Public Market" class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                {{-- Address / Landmark --}}
                <div class="relative">
                    <label for="loc-address-input" class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Address / Landmark</label>
                    <input type="text" id="loc-address-input" name="address" required autocomplete="off" placeholder="Search Nueva Ecija places or enter address..." class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    
                    {{-- Modal Dropdown Autocomplete --}}
                    <div id="modal-autocomplete-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 z-50 bg-white rounded-2xl shadow-xl border border-gray-200 max-h-48 overflow-y-auto divide-y divide-gray-100"></div>
                </div>

                {{-- Interactive Map Pin Picker --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pin Exact Location on Map</label>
                        <span class="text-[10px] text-slate-400 font-bold">Tap or drag pin to adjust</span>
                    </div>
                    <div class="w-full h-44 rounded-2xl overflow-hidden border border-gray-200 shadow-inner relative">
                        <div id="modal-map-container" class="w-full h-full"></div>
                        <div class="absolute bottom-2 left-2 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-xl text-[10px] font-black text-slate-700 border border-gray-200 shadow-xs pointer-events-none" id="modal-map-coords-badge">
                            15.4278, 120.9245
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeSavedLocationModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-black uppercase tracking-wider transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="modal-submit-btn" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-blue-500/25 transition-all cursor-pointer active:scale-95">
                        Save Place
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MapLibre & Script Logic --}}
    <script>
        var modalMapInstance = null;
        var modalMapMarker = null;

        // Search Filter in Saved Destinations List
        var searchInput = document.getElementById('saved-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var query = this.value.toLowerCase().trim();
                var items = document.querySelectorAll('.saved-place-item');
                var matched = 0;

                items.forEach(function(item) {
                    var searchData = item.dataset.search || '';
                    if (!query || searchData.includes(query)) {
                        item.style.display = 'flex';
                        matched++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                var noResults = document.getElementById('saved-search-no-results');
                if (noResults) {
                    noResults.classList.toggle('hidden', matched > 0);
                }
            });
        }

        function openAddSavedLocationModal(category, defaultName, defaultAddress, defaultLat, defaultLng) {
            var modal = document.getElementById('saved-location-modal');
            var form = document.getElementById('saved-location-form');
            var title = document.getElementById('modal-title');
            var submitBtn = document.getElementById('modal-submit-btn');
            var methodInput = document.getElementById('form-method-input');

            form.action = "{{ route('saved-locations.store') }}";
            methodInput.value = "POST";
            title.textContent = "Add Saved Place";
            submitBtn.textContent = "Save Place";

            document.getElementById('loc-name-input').value = defaultName || '';
            document.getElementById('loc-address-input').value = defaultAddress || '';
            
            var cat = category || 'custom';
            var radio = document.querySelector('input[name="type"][value="' + cat + '"]');
            if (radio) radio.checked = true;
            onCategoryChanged(cat);

            // Coordinates
            var lat = defaultLat ? parseFloat(defaultLat) : 15.427800;
            var lng = defaultLng ? parseFloat(defaultLng) : 120.924500;
            document.getElementById('loc-lat-input').value = lat;
            document.getElementById('loc-lng-input').value = lng;
            document.getElementById('modal-map-coords-badge').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);

            modal.classList.remove('hidden');
            setTimeout(initOrUpdateModalMap, 150);
        }

        function openEditSavedLocationModal(loc) {
            var modal = document.getElementById('saved-location-modal');
            var form = document.getElementById('saved-location-form');
            var title = document.getElementById('modal-title');
            var submitBtn = document.getElementById('modal-submit-btn');
            var methodInput = document.getElementById('form-method-input');

            form.action = "/saved-locations/" + loc.id;
            methodInput.value = "PUT";
            title.textContent = "Edit " + loc.name;
            submitBtn.textContent = "Update Place";

            document.getElementById('loc-name-input').value = loc.name;
            document.getElementById('loc-address-input').value = loc.address;

            var radio = document.querySelector('input[name="type"][value="' + loc.type + '"]');
            if (radio) radio.checked = true;
            onCategoryChanged(loc.type);

            var lat = parseFloat(loc.latitude);
            var lng = parseFloat(loc.longitude);
            document.getElementById('loc-lat-input').value = lat;
            document.getElementById('loc-lng-input').value = lng;
            document.getElementById('modal-map-coords-badge').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);

            modal.classList.remove('hidden');
            setTimeout(initOrUpdateModalMap, 150);
        }

        function closeSavedLocationModal() {
            var modal = document.getElementById('saved-location-modal');
            modal.classList.add('hidden');
        }

        function onCategoryChanged(cat) {
            var iconBox = document.getElementById('modal-category-icon');
            var svgs = {
                school: '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>',
                work: '<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                shopping: '<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
                favorite: '<svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',
                custom: '<svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
            };
            if (iconBox && svgs[cat]) {
                iconBox.innerHTML = svgs[cat];
            }

            // Visual toggle: update styles of each category card
            var catStyles = {
                school: { border: '#d97706', bg: '#fef3c7', text: '#92400e', icon: '#d97706' },
                work: { border: '#4f46e5', bg: '#e0e7ff', text: '#3730a3', icon: '#4f46e5' },
                shopping: { border: '#e11d48', bg: '#ffe4e6', text: '#9f1239', icon: '#e11d48' },
                favorite: { border: '#059669', bg: '#d1fae5', text: '#065f46', icon: '#059669' },
                custom: { border: '#2563eb', bg: '#dbeafe', text: '#1e40af', icon: '#2563eb' }
            };

            document.querySelectorAll('.cat-card-box').forEach(function(box) {
                var c = box.dataset.cat;
                var isSelected = (c === cat);
                var svg = box.querySelector('svg');
                var span = box.querySelector('span');
                
                if (isSelected && catStyles[c]) {
                    var st = catStyles[c];
                    box.style.borderColor = st.border;
                    box.style.backgroundColor = st.bg;
                    box.style.boxShadow = '0 2px 8px -2px rgba(0,0,0,0.08), 0 0 0 1px ' + st.border;
                    if (svg) svg.style.color = st.icon;
                    if (span) span.style.color = st.text;
                } else {
                    box.style.borderColor = '#e2e8f0';
                    box.style.backgroundColor = '#ffffff';
                    box.style.boxShadow = 'none';
                    if (svg) svg.style.color = '#64748b';
                    if (span) span.style.color = '#475569';
                }
            });
        }

        function initOrUpdateModalMap() {
            var lat = parseFloat(document.getElementById('loc-lat-input').value) || 15.427800;
            var lng = parseFloat(document.getElementById('loc-lng-input').value) || 120.924500;
            var container = document.getElementById('modal-map-container');
            if (!container) return;

            if (typeof maplibregl === 'undefined') {
                if (window.srhEnsureMaplibre) {
                    window.srhEnsureMaplibre(initOrUpdateModalMap);
                }
                return;
            }

            if (!modalMapInstance) {
                modalMapInstance = new maplibregl.Map({
                    container: 'modal-map-container',
                    style: window._srhMapStyleJson ? JSON.parse(JSON.stringify(window._srhMapStyleJson)) : (window.SRH_MAP_STYLE || '/srh-map-style.json'),
                    center: [lng, lat],
                    zoom: 15,
                    maxTileCacheSize: 500,
                    collectResourceTiming: false,
                    attributionControl: false
                });

                // Suppress missing sprite image warnings by providing a transparent fallback
                modalMapInstance.on('styleimagemissing', function(e) {
                    var imgId = (e && typeof e.id === 'string') ? e.id : '';
                    if (!modalMapInstance.hasImage(imgId)) {
                        try {
                            modalMapInstance.addImage(imgId, { width: 1, height: 1, data: new Uint8Array([0, 0, 0, 0]) }, { sdf: true });
                        } catch(err) {}
                    }
                });

                modalMapInstance.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');

                var el = document.createElement('div');
                el.className = 'w-8 h-8 rounded-full bg-blue-600 border-2 border-white shadow-lg flex items-center justify-center text-white text-xs cursor-grab';
                el.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';

                modalMapMarker = new maplibregl.Marker({ element: el, draggable: true })
                    .setLngLat([lng, lat])
                    .addTo(modalMapInstance);

                modalMapMarker.on('dragend', function() {
                    var lngLat = modalMapMarker.getLngLat();
                    updateModalCoordinates(lngLat.lat, lngLat.lng);
                });

                modalMapInstance.on('click', function(e) {
                    modalMapMarker.setLngLat(e.lngLat);
                    updateModalCoordinates(e.lngLat.lat, e.lngLat.lng);
                });
            } else {
                modalMapInstance.resize();
                modalMapInstance.setCenter([lng, lat]);
                if (modalMapMarker) modalMapMarker.setLngLat([lng, lat]);
            }
        }

        function updateModalCoordinates(lat, lng) {
            document.getElementById('loc-lat-input').value = lat;
            document.getElementById('loc-lng-input').value = lng;
            document.getElementById('modal-map-coords-badge').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
        }

        // Live Geocoding for Modal Address Input
        document.addEventListener('DOMContentLoaded', function() {
            var addrInput = document.getElementById('loc-address-input');
            var dropdown = document.getElementById('modal-autocomplete-dropdown');
            var timer = null;

            if (addrInput && dropdown) {
                addrInput.addEventListener('input', function() {
                    var query = addrInput.value.trim();
                    clearTimeout(timer);
                    if (query.length < 2) {
                        dropdown.classList.add('hidden');
                        return;
                    }

                    timer = setTimeout(function() {
                        fetch('/api/geocode/search?q=' + encodeURIComponent(query))
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                dropdown.innerHTML = '';
                                if (data && data.length > 0) {
                                    data.slice(0, 5).forEach(function(item) {
                                        var btn = document.createElement('button');
                                        btn.type = 'button';
                                        btn.className = 'w-full px-3 py-2.5 text-left hover:bg-blue-50 text-xs font-bold text-slate-800 flex items-center gap-2 transition-colors cursor-pointer border-none bg-transparent';
                                        btn.innerHTML = '<svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span class="truncate">' + item.name + '</span>';
                                        btn.onclick = function() {
                                            addrInput.value = item.name;
                                            if (!document.getElementById('loc-name-input').value) {
                                                document.getElementById('loc-name-input').value = item.name.split(',')[0];
                                            }
                                            if (item.lat && item.lng) {
                                                updateModalCoordinates(parseFloat(item.lat), parseFloat(item.lng));
                                                if (modalMapInstance) {
                                                    modalMapInstance.flyTo({ center: [parseFloat(item.lng), parseFloat(item.lat)], zoom: 16 });
                                                    if (modalMapMarker) modalMapMarker.setLngLat([parseFloat(item.lng), parseFloat(item.lat)]);
                                                }
                                            }
                                            dropdown.classList.add('hidden');
                                        };
                                        dropdown.appendChild(btn);
                                    });
                                    dropdown.classList.remove('hidden');
                                } else {
                                    dropdown.classList.add('hidden');
                                }
                            })
                            .catch(function() {
                                dropdown.classList.add('hidden');
                            });
                    }, 250);
                });

                document.addEventListener('click', function(e) {
                    if (!addrInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
