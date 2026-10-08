<!-- Currently Suspended Drivers Panel -->
@if(isset($drivers) && $drivers->where('compliance_status', 'Suspended')->count() > 0)
    <div class="bg-white rounded-3xl border border-red-200 shadow-sm overflow-hidden" id="suspended-drivers-panel">
        <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-4 border-b border-red-100 bg-red-50/50">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-black text-red-900 leading-tight truncate">
                        Currently Suspended Drivers
                    </h3>
                    <p class="text-[11px] font-bold text-red-700/80">Awaiting resolution or driver appeal</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-black text-red-700 bg-red-100 border border-red-200 shrink-0">
                {{ $drivers->where('compliance_status', 'Suspended')->count() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 sm:p-5">
            @foreach($drivers->where('compliance_status', 'Suspended') as $suspendedDriver)
                <div class="p-4 rounded-2xl border border-gray-200 bg-white flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center font-black text-sm shadow-xs overflow-hidden shrink-0">
                                    @if($suspendedDriver->user && $suspendedDriver->user->profile_photo_url)
                                        <img src="{{ route('user.avatar', [$suspendedDriver->user->id, 'v' => optional($suspendedDriver->user->updated_at)->timestamp]) }}" alt="{{ $suspendedDriver->full_name }}" class="w-full h-full object-cover rounded-xl" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                        <span class="font-black text-white text-xs leading-none uppercase hidden">
                                            {{ strtoupper(substr($suspendedDriver->full_name, 0, 1)) }}
                                        </span>
                                    @else
                                        <span class="font-black text-white text-xs leading-none uppercase">
                                            {{ strtoupper(substr($suspendedDriver->full_name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-slate-900 text-sm truncate">{{ $suspendedDriver->full_name }}</div>
                                    <div class="text-[11px] text-blue-600 font-bold">MTOP: {{ $suspendedDriver->mtop_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-lg text-[10px] font-black uppercase shrink-0">SUSPENDED</span>
                        </div>
                        
                        @if($suspendedDriver->suspension_reason)
                            <div class="text-[11px] text-red-700 font-bold mt-2 bg-red-50 p-2.5 rounded-xl border border-red-100">
                                Reason: {{ $suspendedDriver->suspension_reason }}
                            </div>
                        @endif

                        @if($suspendedDriver->appeal_message)
                            <div class="mt-2 p-3 rounded-xl bg-amber-50/90 border border-amber-200 text-xs space-y-2">
                                <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-[10px] uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Driver Appeal Submitted ({{ $suspendedDriver->appealed_at ? $suspendedDriver->appealed_at->format('M d, Y g:i A') : 'recently' }}):</span>
                                </div>
                                <p class="font-semibold text-amber-950 italic">"{{ $suspendedDriver->appeal_message }}"</p>
                                
                                @if(is_array($suspendedDriver->appeal_attachments) && count($suspendedDriver->appeal_attachments) > 0)
                                    <div class="pt-1.5 border-t border-amber-200/80 space-y-1">
                                        <span class="text-[10px] font-black text-amber-900 uppercase tracking-wider block">Attached Proof / Documents:</span>
                                        <div class="flex items-center gap-1.5 flex-wrap w-full max-w-full overflow-hidden">
                                            @foreach($suspendedDriver->appeal_attachments as $att)
                                                <a href="{{ $att['url'] }}" onclick="openAttachmentPreviewModal('{{ $att['url'] }}', '{{ addslashes($att['name']) }}', event)" class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-white border border-amber-300 text-amber-950 rounded-lg text-[11px] font-bold hover:bg-amber-100 transition shadow-2xs text-decoration-none cursor-pointer max-w-full overflow-hidden">
                                                    <span class="text-amber-600 shrink-0">📎</span>
                                                    <span class="truncate min-w-0 max-w-[120px] sm:max-w-[180px] shrink-1">{{ $att['name'] }}</span>
                                                    <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-2 shrink-0 pt-2 border-t border-gray-100">
                        <form action="{{ route('drivers.unsuspend', $suspendedDriver) }}" method="POST" class="report-action-form">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase shadow-xs transition flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                <span>Unsuspend &amp; Reactivate</span>
                            </button>
                        </form>
                        <form action="{{ route('drivers.destroy', $suspendedDriver) }}" method="POST" class="report-action-form" onsubmit="return confirm('Are you sure you want to permanently remove driver {{ addslashes($suspendedDriver->full_name) }}?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-600 hover:text-white rounded-xl text-xs font-black uppercase shadow-xs transition flex items-center gap-1 cursor-pointer">
                                <span>Remove</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div id="suspended-drivers-panel" class="hidden"></div>
@endif