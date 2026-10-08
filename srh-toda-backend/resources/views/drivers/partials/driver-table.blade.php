@php
    $activeDrivers = $drivers->where('compliance_status', '!=', 'Removed');
    // Sort drivers: Pending first, then Approved, then Suspended, then Rejected
    $sortedDrivers = $activeDrivers->sortBy(function($d) {
        $status = strtolower($d->compliance_status ?? 'pending');
        if ($status === 'pending') return 1;
        if ($status === 'approved') return 2;
        if ($status === 'suspended') return 3;
        if ($status === 'rejected') return 4;
        return 5;
    });

    $countAll = $activeDrivers->count();
    $countPending = $activeDrivers->where('compliance_status', 'Pending')->count();
    $countApproved = $activeDrivers->where('compliance_status', 'Approved')->count();
    $countSuspended = $activeDrivers->where('compliance_status', 'Suspended')->count();
    $countRejected = $activeDrivers->where('compliance_status', 'Rejected')->count();
@endphp

<div class="space-y-4" x-data="{
    driverStatusFilter: 'all',
    setDriverFilter(status) {
        this.driverStatusFilter = status;
        this.applyFilter();
    },
    applyFilter() {
        const status = this.driverStatusFilter;
        const search = (document.getElementById('driverSearchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.searchable-driver-item');
        
        rows.forEach(row => {
            const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
            const rowText = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            
            const matchesStatus = (status === 'all' || rowStatus === status.toLowerCase());
            const matchesSearch = (!search || rowText.includes(search));
            
            if (matchesStatus && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}" x-init="$watch('driverStatusFilter', () => applyFilter())">

    <!-- ─── HEADER & SEARCH BAR ─── -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">TODA Fleet Drivers</h3>
            </div>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Manage registered fleet members, verify applicants, and review driver credentials</p>
        </div>

        <!-- Search Input -->
        <div class="relative w-full lg:w-80">
            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-gray-400" style="left: 14px !important;">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" id="driverSearchInput" 
                   @input="applyFilter()"
                   aria-label="Search drivers by name, MTOP, or status"
                   style="padding-left: 2.75rem !important; min-height: 42px;"
                   class="block w-full pr-4 py-2.5 text-xs font-bold text-gray-900 border border-gray-200 rounded-2xl bg-gray-50/80 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition placeholder:text-gray-400"
                   placeholder="Search driver name, MTOP, or status...">
        </div>
    </div>

    <!-- ─── STATUS FILTER PILLS (Non-scrollable, Natural Wrap) ─── -->
    <div class="flex flex-wrap items-center gap-1.5 text-xs">
        <!-- ALL -->
        <button type="button" @click="setDriverFilter('all')"
                :style="driverStatusFilter === 'all' ? 'background-color: #0f172a !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
            <span>All Drivers</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                  :style="driverStatusFilter === 'all' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                {{ $countAll }}
            </span>
        </button>

        <!-- PENDING -->
        <button type="button" @click="setDriverFilter('pending')"
                :style="driverStatusFilter === 'pending' ? 'background-color: #f59e0b !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
            <span>Pending Applicants</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                  :style="driverStatusFilter === 'pending' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                {{ $countPending }}
            </span>
        </button>

        <!-- APPROVED / ACTIVE -->
        <button type="button" @click="setDriverFilter('approved')"
                :style="driverStatusFilter === 'approved' ? 'background-color: #059669 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
            <span>Active / Approved</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                  :style="driverStatusFilter === 'approved' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                {{ $countApproved }}
            </span>
        </button>

        <!-- SUSPENDED -->
        <button type="button" @click="setDriverFilter('suspended')"
                :style="driverStatusFilter === 'suspended' ? 'background-color: #dc2626 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
            <span>Suspended</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                  :style="driverStatusFilter === 'suspended' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                {{ $countSuspended }}
            </span>
        </button>

        <!-- DECLINED -->
        @if($countRejected > 0)
            <button type="button" @click="setDriverFilter('rejected')"
                    :style="driverStatusFilter === 'rejected' ? 'background-color: #e11d48 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
                <span>Declined</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                      :style="driverStatusFilter === 'rejected' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                    {{ $countRejected }}
                </span>
            </button>
        @endif
    </div>

    <!-- ─── DESKTOP TABLE VIEW (md and up) ─── -->
    <div class="hidden md:block w-full overflow-x-auto bg-white rounded-2xl border border-gray-200 shadow-xs">
        <table class="w-full min-w-[700px] border-collapse text-left">
            <thead class="bg-gray-50/80 border-b border-gray-100">
                <tr>
                    <th class="py-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Driver Profile</th>
                    <th class="py-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Documents</th>
                    <th class="py-3 px-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse($sortedDrivers as $driver)
                    @php
                        $status = strtolower($driver->compliance_status ?? 'pending');
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition searchable-driver-item"
                        data-status="{{ $status }}"
                        data-search="{{ strtolower($driver->full_name . ' ' . $driver->mtop_number . ' ' . $driver->compliance_status . ' ' . ($driver->user->email ?? '')) }}">
                        
                        <!-- 1. Driver Name & MTOP -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm shadow-xs shrink-0 overflow-hidden border border-white {{ $status === 'approved' ? 'bg-emerald-600 text-white' : ($status === 'suspended' ? 'bg-red-600 text-white' : ($status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-gradient-to-tr from-amber-500 to-orange-500 text-white')) }}">
                                    @if($driver->user && $driver->user->profile_photo_url)
                                        <img src="{{ route('user.avatar', [$driver->user->id, 'v' => optional($driver->user->updated_at)->timestamp]) }}" alt="{{ $driver->full_name }}" class="w-full h-full object-cover rounded-2xl" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                        <span class="font-black text-white text-base leading-none uppercase hidden">
                                            {{ strtoupper(substr($driver->full_name, 0, 1)) }}
                                        </span>
                                    @else
                                        <span class="font-black text-white text-base leading-none uppercase">
                                            {{ strtoupper(substr($driver->full_name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-black text-gray-900 leading-tight">{{ $driver->full_name }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        @if($driver->mtop_number)
                                            <span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-700 font-extrabold text-[10px] border border-blue-200/60">MTOP: {{ $driver->mtop_number }}</span>
                                        @endif
                                        <span class="text-[10px] text-gray-400 font-medium">Joined {{ $driver->created_at ? $driver->created_at->format('M d, Y') : 'recently' }}</span>
                                    </div>
                                    @if($driver->user && $driver->user->email)
                                        <span class="text-[10px] text-gray-400 font-medium block truncate max-w-[200px]">{{ $driver->user->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- 2. Status Badge & Details -->
                        <td class="py-3.5 px-4">
                            @if($status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Active Driver
                                </span>
                            @elseif($status === 'suspended')
                                <div class="space-y-1.5 max-w-xs">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span>
                                        Suspended
                                    </span>
                                    @if($driver->suspension_reason)
                                        <p class="text-[10px] text-red-700 font-medium line-clamp-2">Reason: {{ $driver->suspension_reason }}</p>
                                    @endif

                                    @if($driver->appeal_message)
                                        <div class="p-2 bg-amber-50/90 rounded-xl border border-amber-200 text-[11px] space-y-1 text-amber-950 mt-1">
                                            <div class="flex items-center gap-1 font-extrabold text-[9px] uppercase tracking-wider text-amber-800">
                                                <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Appeal Submitted ({{ $driver->appealed_at ? $driver->appealed_at->format('M d, Y g:i A') : 'Recently' }}):</span>
                                            </div>
                                            <p class="italic text-amber-900 font-semibold text-[10px] leading-tight">"{{ $driver->appeal_message }}"</p>
                                            @if(is_array($driver->appeal_attachments) && count($driver->appeal_attachments) > 0)
                                                <div class="flex items-center gap-1 flex-wrap pt-1 border-t border-amber-200/60">
                                                    @foreach($driver->appeal_attachments as $att)
                                                        <button type="button" onclick="openAttachmentPreviewModal('{{ $att['url'] }}', '{{ addslashes($att['name']) }}', event)" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-white border border-amber-300 text-amber-950 rounded-md text-[9px] font-bold hover:bg-amber-100 transition cursor-pointer">
                                                            <span class="text-amber-600">📎</span>
                                                            <span class="truncate max-w-[90px]">{{ $att['name'] }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @elseif($status === 'rejected')
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                        Declined
                                    </span>
                                    @if($driver->suspension_reason)
                                        <p class="text-[10px] text-rose-700 font-medium line-clamp-1 max-w-xs">{{ $driver->suspension_reason }}</p>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending Review
                                </span>
                            @endif
                        </td>

                        <!-- 3. Documents (MTOP & License) -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($driver->mtop_certificate_url)
                                    <button type="button" onclick="openAttachmentPreviewModal('{{ route('drivers.documents.show', ['driver' => $driver->id, 'type' => 'mtop']) }}', 'MTOP Certificate - {{ addslashes($driver->full_name) }}', event)" 
                                            class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-black uppercase flex items-center gap-1 transition cursor-pointer">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>MTOP</span>
                                    </button>
                                @endif

                                @if($driver->drivers_license_url)
                                    <button type="button" onclick="openAttachmentPreviewModal('{{ route('drivers.documents.show', ['driver' => $driver->id, 'type' => 'license']) }}', 'Driver\'s License - {{ addslashes($driver->full_name) }}', event)" 
                                            class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-black uppercase flex items-center gap-1 transition cursor-pointer">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>License</span>
                                    </button>
                                @endif
                            </div>
                        </td>

                        <!-- 4. Direct Action Buttons -->
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                @if($status === 'pending')
                                    <!-- Approve Button -->
                                    <form action="{{ route('drivers.update', $driver) }}" method="POST" class="inline-block" onsubmit="handleRideActionSubmit(event, this)">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Approved">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-black uppercase tracking-wider shadow-xs transition cursor-pointer border-none">
                                            Approve
                                        </button>
                                    </form>
                                    <!-- Reject Button -->
                                    <button type="button" onclick="openRejectApplicantModal({{ $driver->id }}, '{{ addslashes($driver->full_name) }}')" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-[11px] font-black uppercase tracking-wider transition cursor-pointer border-none">
                                        Reject
                                    </button>
                                @elseif($status === 'approved')
                                    <!-- Suspend Button -->
                                    <button type="button" onclick="openDirectSuspendModal({{ $driver->id }}, '{{ addslashes($driver->full_name) }}')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-[11px] font-black uppercase tracking-wider transition cursor-pointer">
                                        Suspend
                                    </button>
                                @elseif($status === 'suspended')
                                    <!-- Unsuspend / Reactivate -->
                                    <form action="{{ route('drivers.unsuspend', $driver) }}" method="POST" class="inline-block" onsubmit="handleRideActionSubmit(event, this)">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-black uppercase tracking-wider shadow-xs transition cursor-pointer border-none">
                                            Reactivate
                                        </button>
                                    </form>
                                    <!-- Remove Button -->
                                    <button type="button" onclick="openRemoveApplicantModal({{ $driver->id }}, '{{ addslashes($driver->full_name) }}')" class="px-2.5 py-1.5 bg-slate-100 hover:bg-red-100 text-slate-600 hover:text-red-700 rounded-xl text-[11px] font-bold transition cursor-pointer border-none">
                                        Remove
                                    </button>
                                @elseif($status === 'rejected')
                                    <!-- Re-approve -->
                                    <form action="{{ route('drivers.update', $driver) }}" method="POST" class="inline-block" onsubmit="handleRideActionSubmit(event, this)">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Approved">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-black uppercase tracking-wider shadow-xs transition cursor-pointer border-none">
                                            Accept
                                        </button>
                                    </form>
                                    <!-- Remove -->
                                    <button type="button" onclick="openRemoveApplicantModal({{ $driver->id }}, '{{ addslashes($driver->full_name) }}')" class="px-2.5 py-1.5 bg-slate-800 text-white rounded-xl text-[11px] font-black uppercase transition cursor-pointer border-none">
                                        Remove
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400 italic">No TODA drivers registered in the system.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ─── MOBILE DRIVER CARDS (Below md) ─── -->
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($sortedDrivers as $driver)
            @php
                $status = strtolower($driver->compliance_status ?? 'pending');
            @endphp
            <div class="p-4 rounded-2xl bg-white border border-gray-200/80 shadow-xs space-y-3 searchable-driver-item"
                 data-status="{{ $status }}"
                 data-search="{{ strtolower($driver->full_name . ' ' . $driver->mtop_number . ' ' . $driver->compliance_status . ' ' . ($driver->user->email ?? '')) }}">
                
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-2 border-b border-gray-100 pb-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm shadow-xs shrink-0 overflow-hidden border border-white {{ $status === 'approved' ? 'bg-emerald-600 text-white' : ($status === 'suspended' ? 'bg-red-600 text-white' : ($status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-gradient-to-tr from-amber-500 to-orange-500 text-white')) }}">
                            @if($driver->user && $driver->user->profile_photo_url)
                                <img src="{{ route('user.avatar', [$driver->user->id, 'v' => optional($driver->user->updated_at)->timestamp]) }}" alt="{{ $driver->full_name }}" class="w-full h-full object-cover rounded-2xl" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span class="font-black text-white text-base leading-none uppercase hidden">
                                    {{ strtoupper(substr($driver->full_name, 0, 1)) }}
                                </span>
                            @else
                                <span class="font-black text-white text-base leading-none uppercase">
                                    {{ strtoupper(substr($driver->full_name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-gray-900 leading-tight">{{ $driver->full_name }}</h4>
                            @if($driver->mtop_number)
                                <span class="text-[11px] font-extrabold text-blue-600 block mt-0.5">MTOP NO: {{ $driver->mtop_number }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Status Badge -->
                    @if($status === 'approved')
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 shrink-0">Active</span>
                    @elseif($status === 'suspended')
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-800 shrink-0">Suspended</span>
                    @elseif($status === 'rejected')
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 shrink-0">Declined</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 shrink-0">Pending</span>
                    @endif
                </div>

                <!-- Suspension / Appeal details note -->
                @if($status === 'suspended')
                    @if($driver->suspension_reason)
                        <div class="p-2.5 bg-red-50 rounded-xl border border-red-100 text-xs">
                            <span class="text-[10px] font-bold text-red-700 uppercase tracking-wider block">Suspension Reason:</span>
                            <p class="font-medium text-red-950 mt-0.5">{{ $driver->suspension_reason }}</p>
                        </div>
                    @endif

                    @if($driver->appeal_message)
                        <div class="p-3 bg-amber-50/90 rounded-2xl border border-amber-200 text-xs space-y-2">
                            <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-[10px] uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Driver Appeal Submitted ({{ $driver->appealed_at ? $driver->appealed_at->format('M d, Y g:i A') : 'recently' }}):</span>
                            </div>
                            <p class="font-semibold text-amber-950 italic leading-relaxed">"{{ $driver->appeal_message }}"</p>
                            
                            @if(is_array($driver->appeal_attachments) && count($driver->appeal_attachments) > 0)
                                <div class="pt-2 border-t border-amber-200/80 space-y-1">
                                    <span class="text-[10px] font-black text-amber-900 uppercase tracking-wider block">Attached Proof / Documents:</span>
                                    <div class="flex items-center gap-1.5 flex-wrap w-full">
                                        @foreach($driver->appeal_attachments as $att)
                                            <button type="button" onclick="openAttachmentPreviewModal('{{ $att['url'] }}', '{{ addslashes($att['name']) }}', event)" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-amber-300 text-amber-950 rounded-xl text-xs font-bold hover:bg-amber-100 transition shadow-2xs cursor-pointer">
                                                <span class="text-amber-600 shrink-0">📎</span>
                                                <span class="truncate max-w-[140px]">{{ $att['name'] }}</span>
                                                <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                @if($status === 'rejected' && $driver->suspension_reason)
                    <div class="p-2.5 bg-rose-50 rounded-xl border border-rose-100 text-xs">
                        <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Rejection Reason:</span>
                        <p class="font-medium text-rose-950 mt-0.5">{{ $driver->suspension_reason }}</p>
                    </div>
                @endif

                <!-- Document Badges & Action Buttons -->
                <div class="flex items-center justify-between gap-2 pt-1">
                    <!-- Documents -->
                    <div class="flex items-center gap-1.5">
                        @if($driver->mtop_certificate_url)
                            <button type="button" onclick="openAttachmentPreviewModal('{{ route('drivers.documents.show', ['driver' => $driver->id, 'type' => 'mtop']) }}', 'MTOP Certificate - {{ addslashes($driver->full_name) }}', event)" 
                                    class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-black uppercase cursor-pointer">
                                MTOP
                            </button>
                        @endif
                        @if($driver->drivers_license_url)
                            <button type="button" onclick="openAttachmentPreviewModal('{{ route('drivers.documents.show', ['driver' => $driver->id, 'type' => 'license']) }}', 'Driver\'s License - {{ addslashes($driver->full_name) }}', event)" 
                                    class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-black uppercase cursor-pointer">
                                License
                            </button>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1.5">
                        @if($status === 'pending')
                            <form action="{{ route('drivers.update', $driver) }}" method="POST" class="inline-block" onsubmit="handleRideActionSubmit(event, this)">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase shadow-xs border-none cursor-pointer">
                                    Approve
                                </button>
                            </form>
                            <button type="button" onclick="openRejectApplicantModal({{ $driver->id }}, '{{ addslashes($driver->full_name) }}')" class="px-3.5 py-1.5 bg-red-100 text-red-700 rounded-xl text-xs font-black uppercase border-none cursor-pointer">
                                Reject
                            </button>
                        @elseif($status === 'approved')
                            <button type="button" onclick="openDirectSuspendModal({{ $driver->id }}, '{{ addslashes($driver->full_name) }}')" class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs font-black uppercase cursor-pointer">
                                Suspend
                            </button>
                        @elseif($status === 'suspended')
                            <form action="{{ route('drivers.unsuspend', $driver) }}" method="POST" class="inline-block" onsubmit="handleRideActionSubmit(event, this)">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase shadow-xs border-none cursor-pointer">
                                    Reactivate
                                </button>
                            </form>
                        @elseif($status === 'rejected')
                            <form action="{{ route('drivers.update', $driver) }}" method="POST" class="inline-block" onsubmit="handleRideActionSubmit(event, this)">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase shadow-xs border-none cursor-pointer">
                                    Accept
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="p-8 text-center text-gray-500 italic bg-white rounded-2xl border border-gray-100">
                No TODA drivers registered in the system.
            </div>
        @endforelse
    </div>

</div>

<!-- ─── REJECT APPLICANT MODAL ─── -->
<div id="rejectApplicantModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 999999; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.85); overflow: hidden; align-items: center; justify-content: center;">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh; max-height: 85dvh; margin: auto; position: relative; z-index: 1000000;">
        
        <div class="p-6 relative flex items-center justify-between shrink-0" style="background-color: #dc2626 !important; color: #ffffff !important; flex-shrink: 0;">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1" style="background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important;">
                    <span>APPLICATION VERIFICATION</span>
                </div>
                <h3 class="text-xl font-black" style="color: #ffffff !important;">Reject Driver Application</h3>
                <p class="text-xs mt-0.5" style="color: #fee2e2 !important;">Specify official reason for declining document verification</p>
            </div>
            <button type="button" onclick="closeRejectApplicantModal()" class="w-8 h-8 rounded-full flex items-center justify-center font-bold border-none cursor-pointer" style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;">✕</button>
        </div>

        <form id="rejectApplicantForm" method="POST" action="" onsubmit="submitRejectApplicantForm(event, this)" class="p-6 space-y-4 flex-1 overflow-y-auto">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="Rejected">

            <div class="bg-red-50/80 border border-red-200/80 rounded-2xl p-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-wider text-red-600">Applicant Name</p>
                    <h4 id="reject_applicant_name_text" class="text-sm font-black text-slate-900 truncate">--</h4>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="reject_suspension_reason" class="block text-xs font-black text-gray-700 uppercase">Reason for Rejection <span class="text-red-500">*</span></label>
                <textarea id="reject_suspension_reason" name="suspension_reason" required rows="3" placeholder="Provide clear reason (e.g. Expired Driver's License, Blurred MTOP Document)..." class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-medium text-gray-900 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeRejectApplicantModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 cursor-pointer">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md cursor-pointer">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<!-- ─── REMOVE APPLICANT MODAL ─── -->
<div id="removeApplicantModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 999999; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.85); overflow: hidden; align-items: center; justify-content: center;">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh; max-height: 85dvh; margin: auto; position: relative; z-index: 1000000;">
        
        <div class="p-6 relative flex items-center justify-between shrink-0" style="background-color: #0f172a !important; color: #ffffff !important; flex-shrink: 0;">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1" style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;">
                    <span>DATABASE ACTION</span>
                </div>
                <h3 class="text-xl font-black" style="color: #ffffff !important;">Remove Driver Record</h3>
                <p class="text-xs mt-0.5" style="color: #94a3b8 !important;">Permanently remove driver application from system</p>
            </div>
            <button type="button" onclick="closeRemoveApplicantModal()" class="w-8 h-8 rounded-full flex items-center justify-center font-bold border-none cursor-pointer" style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;">✕</button>
        </div>

        <form id="removeApplicantForm" method="POST" action="" onsubmit="submitRemoveApplicantForm(event, this)" class="p-6 space-y-4 flex-1 overflow-y-auto">
            @csrf

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-800 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">Removing Driver</p>
                    <h4 id="remove_applicant_name_text" class="text-sm font-black text-slate-900 truncate">--</h4>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="remove_rejection_reason" class="block text-xs font-black text-gray-700 uppercase">Reason for Removal (Optional)</label>
                <textarea id="remove_rejection_reason" name="rejection_reason" rows="2" placeholder="Note reason for removal..." class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-medium text-gray-900 focus:ring-slate-500 focus:border-slate-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeRemoveApplicantModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 cursor-pointer">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md cursor-pointer">Confirm Removal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectApplicantModal(driverId, driverName) {
        const modal = document.getElementById('rejectApplicantModal');
        const form = document.getElementById('rejectApplicantForm');
        const nameText = document.getElementById('reject_applicant_name_text');

        if (modal && form && nameText) {
            form.action = '/drivers/' + driverId;
            nameText.textContent = driverName;
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
        }
    }

    function closeRejectApplicantModal() {
        document.body.style.overflow = '';
        const modal = document.getElementById('rejectApplicantModal');
        if (modal) modal.classList.add('hidden');
    }

    function submitRejectApplicantForm(e, form) {
        e.preventDefault();
        const actionUrl = form.action;
        const formData = new FormData(form);

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-SPA-Request': 'true'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeRejectApplicantModal();
            if (window.createSlidingToast) {
                window.createSlidingToast('Driver application declined.', 'info');
            }
            if (window.refreshApplicantsArea) window.refreshApplicantsArea();
        })
        .catch(err => {
            alert('Failed to update driver status.');
        });
    }

    function openRemoveApplicantModal(driverId, driverName) {
        const modal = document.getElementById('removeApplicantModal');
        const form = document.getElementById('removeApplicantForm');
        const nameText = document.getElementById('remove_applicant_name_text');

        if (modal && form && nameText) {
            form.action = '/drivers/' + driverId + '/remove-application';
            nameText.textContent = driverName;
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
        }
    }

    function closeRemoveApplicantModal() {
        document.body.style.overflow = '';
        const modal = document.getElementById('removeApplicantModal');
        if (modal) modal.classList.add('hidden');
    }

    function submitRemoveApplicantForm(e, form) {
        e.preventDefault();
        const actionUrl = form.action;
        const formData = new FormData(form);

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-SPA-Request': 'true'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeRemoveApplicantModal();
            if (window.createSlidingToast) {
                window.createSlidingToast('Driver removed from database.', 'success');
            }
            if (window.refreshApplicantsArea) window.refreshApplicantsArea();
        })
        .catch(err => {
            alert('Failed to remove driver application.');
        });
    }
</script>