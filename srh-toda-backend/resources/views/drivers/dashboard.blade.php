<x-app-layout>
    @if($driver && $driver->compliance_status === 'Approved')
        <x-slot name="header">
            <x-page-header title="{{ __('SRH LINK-TODA') }}" subtitle="Driver hub">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                </x-slot>
            </x-page-header>
        </x-slot>
    @endif

    @if(!$driver || ($driver->compliance_status === 'Pending' && !$driver->mtop_certificate_url))
        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
            <div class="bg-white border border-slate-200/80 overflow-hidden shadow-sm sm:shadow-md rounded-2xl sm:rounded-3xl p-6 sm:p-10">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-800">Complete Your Application</h3>
                    <p class="text-gray-600">Please upload your documents for Admin review.</p>
                </div>

                <form action="{{ route('drivers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="full_name" :value="__('Full Name (as per License)')" />
                        <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" required />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="mtop_number" :value="__('MTOP Body Number')" />
                        <x-text-input id="mtop_number" class="block mt-1 w-full" type="text" name="mtop_number" required placeholder="01234" maxlength="6" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-6 border-2 border-dashed border-gray-300 rounded-2xl text-center hover:border-blue-500 transition" id="mtop_box">
                            <label class="cursor-pointer block">
                                <div id="mtop_icon" class="w-10 h-10 mx-auto mb-2 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span id="mtop_filename" class="text-sm font-bold text-gray-700">Upload MTOP Certificate</span>
                                <p class="text-[10px] text-gray-500 font-medium mt-1">Accepted: JPG, PNG, WEBP, HEIC, PDF, DOC, DOCX (Max 20MB)</p>
                                <input type="file" name="mtop_certificate" class="hidden" required aria-label="Upload MTOP Certificate"
                                       accept=".jpg,.jpeg,.png,.webp,.heic,.heif,.pdf,.doc,.docx" onchange="updateFileName(this, 'mtop')">
                            </label>
                        </div>

                        <div class="p-6 border-2 border-dashed border-gray-300 rounded-2xl text-center hover:border-blue-500 transition" id="license_box">
                            <label class="cursor-pointer block">
                                <div id="license_icon" class="w-10 h-10 mx-auto mb-2 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                                    </svg>
                                </div>
                                <span id="license_filename" class="text-sm font-bold text-gray-700">Upload Driver's License</span>
                                <p class="text-[10px] text-gray-500 font-medium mt-1">Accepted: JPG, PNG, WEBP, HEIC, PDF, DOC, DOCX (Max 20MB)</p>
                                <input type="file" name="drivers_license" class="hidden" required aria-label="Upload Driver's License"
                                       accept=".jpg,.jpeg,.png,.webp,.heic,.heif,.pdf,.doc,.docx" onchange="updateFileName(this, 'license')">
                            </label>
                        </div>
                    </div>

                    <x-primary-button class="w-full justify-center py-4 rounded-2xl">
                        Submit Application
                    </x-primary-button>
                </form> 
                
                <div class="mt-6 text-center">
                    <form action="{{ route('identity.cancel') }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        <button type="submit" class="text-sm text-gray-400 hover:text-red-500 underline">
                            Cancel and go back to role selection
                        </button>
                    </form>
                </div>
            </div>
        </div>

    @elseif($driver->compliance_status === 'Pending')
        <style>
            body, .min-h-screen, #spa-content-area, #app-main-content {
                background-color: #ffffff !important;
            }
        </style>

        <!-- 100% UNIFIED PURE WHITE SCREEN -->
        <div class="w-full bg-white min-h-[calc(100vh-120px)] sm:min-h-[calc(100vh-80px)] flex flex-col justify-center items-center px-4 py-6 text-center my-auto">
            <div class="w-full max-w-lg mx-auto my-auto flex flex-col items-center justify-center space-y-4 sm:space-y-6">
                <!-- Illustration Image sized to fit viewport height -->
                <div class="w-full max-w-sm sm:max-w-md mx-auto flex items-center justify-center">
                    <img src="{{ asset('images/driver-verification.png') }}" alt="SRH LINK-TODA Verification" class="max-h-[38vh] sm:max-h-[44vh] w-auto object-contain mx-auto" />
                </div>

                <!-- Title & Subtitle -->
                <div class="space-y-2 px-2">
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">Your profile is under review</h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed max-w-md mx-auto">
                        Your SRH LINK-TODA driver profile has been submitted and is currently being verified by TODA Admin. You will be notified once your account is approved.
                    </p>
                </div>

                <!-- TODA Status Pill -->
                <div class="pt-1">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200/80 shadow-xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        Verification in progress
                    </span>
                </div>
            </div>
        </div>

        <script>
            function checkApplicationStatus() {
                fetch("{{ route('driver.check-status') }}", {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) return null;
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.status && data.status !== 'Pending') {
                            if (window.driverAppInterval) {
                                clearInterval(window.driverAppInterval);
                                window.driverAppInterval = null;
                            }
                            if (window.navigateTo) {
                                window.navigateTo(window.location.href, false, false);
                            } else {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(error => console.error('Error checking status:', error));
            }

            if (window.driverAppInterval) {
                clearInterval(window.driverAppInterval);
            }
            window.driverAppInterval = setInterval(checkApplicationStatus, 5000);
        </script>

    @elseif($driver && $driver->compliance_status === 'Approved')
        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
            <div class="bg-white border border-slate-200/80 overflow-hidden shadow-sm sm:shadow-md rounded-2xl sm:rounded-3xl p-6 sm:p-10">
                <div class="text-center py-4">
                    <h3 class="text-2xl font-black text-gray-800 mb-1">
                        Welcome, {{ $driver->full_name }}
                    </h3>

                    @if($driver->is_online)
                        <p class="text-green-500 font-bold uppercase tracking-widest text-[10px] mb-6">
                            You are currently ONLINE
                        </p>

                        <div id="queue-card" class="mb-8">
                            <p class="text-gray-400 text-[10px] uppercase font-black">Position in Queue</p>
                            <h1 class="text-7xl font-black text-gray-900">#{{ $driver->queue_position }}</h1>
                        </div>

                        <form action="{{ route('drivers.toggle-status') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-red-500 text-white rounded-2xl font-black shadow-lg active:scale-95 transition-all">
                                GO OFFLINE
                            </button>
                        </form>

                        <a href="{{ route('drivers.live-queue') }}" class="block mt-6 text-blue-600 font-bold text-xs underline uppercase">
                            View Live Queue
                        </a>

                    @else
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mb-12">
                            You are currently offline...
                        </p>

                        <div class="flex justify-center mb-8">
                            <form action="{{ route('drivers.toggle-status') }}" method="POST">
                                @csrf
                                <button type="submit" class="group relative flex items-center justify-center w-36 h-36 bg-gray-50 rounded-full border-8 border-white shadow-2xl active:scale-90 transition-all">
                                    <div class="absolute inset-0 rounded-full group-hover:bg-blue-50/50 transition-colors"></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300 group-hover:text-blue-500 transition-colors relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @elseif($driver->compliance_status === 'Rejected')
        <!-- REJECTED DRIVER APPLICATION APPEAL SCREEN -->
        <div class="min-h-[calc(100vh-112px)] w-full bg-white flex flex-col justify-between items-center px-4 py-8 text-center -mt-2 -mb-20 sm:-mb-8">
            <div class="w-full max-w-md mx-auto my-auto space-y-6">
                <!-- Rejected Banner -->
                <div class="p-6 rounded-3xl bg-rose-50/80 text-slate-900 text-center shadow-xs space-y-3 relative overflow-hidden border border-rose-200/80">
                    <div class="w-14 h-14 rounded-2xl bg-white text-rose-600 flex items-center justify-center mx-auto border border-rose-200 shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-rose-100/90 text-rose-800 border border-rose-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                            Application Declined
                        </span>
                        <h3 class="text-2xl font-extrabold mt-3 text-slate-900 tracking-tight">Application Needs Re-evaluation</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Your driver application was not approved during initial document verification.</p>
                    </div>
                </div>

                <!-- Rejection Reason Card -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-left space-y-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Official Rejection Notice</span>
                    <p class="text-xs font-semibold text-slate-800 bg-slate-50 p-3.5 rounded-xl border border-slate-200/60 leading-relaxed">
                        "{{ $driver->suspension_reason ?? 'Your submitted documents or details did not pass verification. Please submit an appeal with explanation or corrected information.' }}"
                    </p>
                </div>

                <!-- Appeal Section -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-left space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h4 class="text-base font-extrabold text-slate-900">Submit an Appeal</h4>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Provide additional explanation or request re-verification from TODA Admin.</p>
                    </div>

                    @if($driver->appeal_status === 'Pending')
                        <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                            <div class="flex items-center gap-2 text-amber-800 font-semibold text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Appeal Pending Review</span>
                            </div>
                            <p class="text-xs font-medium text-amber-950 italic leading-relaxed">"{{ $driver->appeal_message }}"</p>
                            <p class="text-[11px] text-amber-700 font-medium">Submitted {{ $driver->appealed_at ? $driver->appealed_at->diffForHumans() : 'recently' }}. TODA Admin will re-evaluate your application shortly.</p>
                        </div>
                    @else
                        <form action="{{ route('drivers.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ attachedFiles: [] }">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Your Appeal / Explanation <span class="text-rose-500">*</span></label>
                                
                                <!-- Gemini-style Container Box with Textarea & Attachment Plus Button -->
                                <div class="relative rounded-2xl border border-slate-300 bg-slate-50/50 focus-within:bg-white focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all p-3 space-y-2">
                                    
                                    <!-- Real-time Attachment Preview Chips -->
                                    <div x-show="attachedFiles.length > 0" class="flex items-center gap-1.5 flex-wrap pb-1.5 border-b border-slate-200/80">
                                        <template x-for="(file, idx) in attachedFiles" :key="idx">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-blue-50 text-blue-900 border border-blue-200 text-xs font-extrabold shadow-xs">
                                                <span class="text-blue-600">📎</span>
                                                <span class="max-w-[140px] truncate" x-text="file.name"></span>
                                                <button type="button" @click="attachedFiles.splice(idx, 1)" class="text-slate-400 hover:text-rose-600 font-bold ml-1 cursor-pointer">&times;</button>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Main Textarea -->
                                    <textarea name="appeal_message" required aria-label="Appeal explanation message" rows="4" placeholder="Explain why your application should be approved or detail corrected documents/details..." class="w-full bg-transparent text-xs font-medium text-slate-900 outline-none border-none resize-none p-0 focus:ring-0"></textarea>

                                    <!-- Gemini-Style Bottom Action Bar with Plus Button -->
                                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60">
                                        <!-- Gemini Plus Attachment Button -->
                                        <button type="button" @click="$refs.appealFileInput.click()" title="Attach document or proof file" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200/80 hover:bg-blue-600 hover:text-white text-slate-700 text-xs font-black transition cursor-pointer border-none group">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span>Attach Proof</span>
                                        </button>

                                        <span class="text-[10px] text-slate-400 font-medium">Images, PDF (Max 10MB)</span>

                                        <input x-ref="appealFileInput" type="file" name="appeal_attachments[]" multiple accept="image/*,.pdf,.doc,.docx" class="hidden" aria-label="Appeal file attachments" @change="attachedFiles = Array.from($event.target.files)">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-6 rounded-2xl font-black text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md transition active:scale-98 cursor-pointer border-none uppercase tracking-wider">
                                Submit Appeal to TODA Admin
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    @elseif($driver->compliance_status === 'Removed')
        <style>
            html, body {
                overflow: hidden !important;
                height: 100% !important;
            }
            header#navbar, .srh-nav, .desktop-nav-tabs, .nav-item-link, .srh-nav-footer,
            #spa-content-area .page-transition-wrapper {
                visibility: hidden !important;
                pointer-events: none !important;
            }
            body, .min-h-screen, #spa-content-area, #app-main-content {
                background-color: #0f172a !important;
            }
        </style>

        {{-- FULL-SCREEN LOCKOUT: APPLICANT CANNOT NAVIGATE OR APPLY ANYMORE --}}
        <div class="fixed inset-0" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 2147482900; background: linear-gradient(150deg, #0f172a 0%, #1e293b 45%, #1e3a8a 100%) !important; display: flex; align-items: center; justify-content: center; padding: 1.5rem; overflow: hidden;">
            <div class="w-full max-w-lg mx-auto text-center space-y-6 relative z-10">
                <!-- Lock Icon -->
                <div class="mx-auto w-20 h-20 rounded-3xl flex items-center justify-center shadow-2xl border" style="background-color: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                    <svg class="w-10 h-10" fill="none" stroke="#fbbf24" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0" />
                    </svg>
                </div>

                <div class="space-y-2">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider" style="background-color: rgba(251, 191, 36, 0.15); border: 1px solid rgba(251, 191, 36, 0.4); color: #fbbf24 !important;">
                        Application Removed
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight" style="color: #ffffff !important;">
                        You Can No Longer Apply
                    </h1>
                    <p class="text-xs sm:text-sm font-medium leading-relaxed max-w-md mx-auto" style="color: #cbd5e1 !important;">
                        Your driver application has been permanently removed from the SRH LINK-TODA system. You are no longer able to submit an application or use the app's services.
                    </p>
                </div>

                <!-- Official Notice Card -->
                <div class="p-5 rounded-2xl text-left space-y-2 border" style="background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                    <span class="block text-[10px] font-black uppercase tracking-wider" style="color: #93c5fd !important;">Official Removal Notice</span>
                    <p class="text-xs font-semibold leading-relaxed" style="color: #e2e8f0 !important;">
                        "{{ $driver->suspension_reason ?? 'Your driver application has been removed by the SRH TODA Administration.' }}"
                    </p>
                </div>

                <!-- Contact Admin Card -->
                <div class="p-5 rounded-3xl text-left space-y-3 border" style="background: rgba(255,255,255,0.97) !important; border: 1px solid rgba(226,232,240,0.9); box-shadow: 0 24px 48px -16px rgba(0,0,0,0.45);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0" style="background-color: #dc2626; color: #ffffff;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.572v-2.047a2.25 2.25 0 00-2.25-2.25h-1.5m-13.5 4.297v-2.047a2.25 2.25 0 012.25-2.25h1.5m-3.75 4.297V12m18 5.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black" style="color: #0f172a !important;">Need Assistance?</h4>
                            <p class="text-[11px] font-medium" style="color: #64748b !important;">For any concerns or clarification regarding your application</p>
                        </div>
                    </div>
                    <div class="p-3.5 rounded-2xl text-xs font-bold leading-relaxed" style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;">
                        Please visit or contact the <span style="font-weight: 900;">SRH TODA Administration</span> office directly. Our team will assist you with anything necessary regarding your account and application.
                    </div>
                </div>
            </div>
        </div>

    @elseif($driver->compliance_status === 'Suspended')
        <style>
            html, body {
                overflow-y: auto !important;
                overflow-x: hidden !important;
                height: auto !important;
            }
            body, .min-h-screen, #spa-content-area, #app-main-content {
                background-color: #f8fafc !important;
            }
            #app-main-content {
                padding-bottom: 6rem !important;
                overflow-y: auto !important;
            }
        </style>

        {{-- RESPONSIVE STREAMLINED SUSPENDED DRIVER VIEW --}}
        <div class="w-full bg-slate-50 min-h-[calc(100vh-60px)] sm:min-h-screen flex flex-col justify-center items-center px-3 sm:px-6 py-6 pb-36 sm:pb-24 overflow-y-auto my-auto">
            <div class="w-full max-w-2xl mx-auto my-auto space-y-4">
                
                <!-- Sleek Compact Banner Header -->
                <div class="bg-white border border-rose-200 rounded-3xl p-4 sm:p-6 shadow-xs relative overflow-hidden space-y-3 text-left">
                    <div class="flex items-center justify-between gap-3 border-b border-rose-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200/80 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    Account Suspended
                                </span>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-0.5">Your Driver Account is Suspended</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Official Suspension Reason -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider block">Official Suspension Reason</span>
                        <p class="text-xs sm:text-sm font-semibold text-slate-800 bg-rose-50/60 p-3.5 rounded-2xl border border-rose-200/60 leading-relaxed">
                            "{{ $driver->suspension_reason ?? 'Administrative Suspension by TODA Admin. Please submit an appeal below or visit the TODA office.' }}"
                        </p>
                    </div>
                </div>

                <!-- Reinstatement Appeal Section -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-4 sm:p-6 shadow-xs space-y-4 text-left">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900">Reinstatement Appeal</h4>
                            <p class="text-xs text-slate-500 font-medium">Provide explanation or attach supporting proof for TODA Admin review.</p>
                        </div>
                    </div>

                    @if($driver->appeal_status === 'Pending')
                        <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200/90 space-y-3">
                            <div class="flex items-center gap-2 text-amber-900 font-black text-xs uppercase tracking-wider">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>Appeal Pending Review</span>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-amber-200/80 space-y-2">
                                <p class="text-xs font-semibold text-amber-950 italic leading-relaxed">"{{ $driver->appeal_message }}"</p>
                                
                                @if(is_array($driver->appeal_attachments) && count($driver->appeal_attachments) > 0)
                                    <div class="pt-2 border-t border-amber-100 space-y-1.5">
                                        <span class="text-[10px] font-black text-amber-900 uppercase tracking-wider block">Submitted Attachments ({{ count($driver->appeal_attachments) }}):</span>
                                        <div class="flex items-center gap-2 flex-wrap w-full max-w-full overflow-hidden">
                                            @foreach($driver->appeal_attachments as $att)
                                                <a href="{{ $att['url'] }}" onclick="openAttachmentPreviewModal('{{ $att['url'] }}', '{{ addslashes($att['name']) }}', event)" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-300 text-amber-950 rounded-xl text-xs font-black hover:bg-amber-100 transition shadow-2xs text-decoration-none cursor-pointer max-w-full overflow-hidden">
                                                    <span class="text-amber-600 shrink-0">📎</span>
                                                    <span class="truncate min-w-0 max-w-[140px] sm:max-w-[200px] shrink-1">{{ $att['name'] }}</span>
                                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <p class="text-[11px] text-amber-700 font-medium">Submitted {{ $driver->appealed_at ? $driver->appealed_at->diffForHumans() : 'recently' }}. TODA Admin will review your appeal shortly.</p>
                        </div>
                    @else
                        <form action="{{ route('drivers.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="handleRideActionSubmit(event, this)" x-data="{ attachedFiles: [] }">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Your Appeal / Explanation <span class="text-rose-500">*</span></label>
                                
                                <div class="relative rounded-2xl border border-slate-300 bg-slate-50/50 focus-within:bg-white focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all p-3 space-y-2">
                                    <div x-show="attachedFiles.length > 0" class="flex items-center gap-1.5 flex-wrap pb-2 border-b border-slate-200/80 w-full max-w-full overflow-hidden">
                                        <template x-for="(file, idx) in attachedFiles" :key="idx">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-xl bg-blue-50 text-blue-900 border border-blue-200 text-xs font-extrabold shadow-2xs max-w-full overflow-hidden">
                                                <span class="text-blue-600 shrink-0">📎</span>
                                                <span class="truncate min-w-0 max-w-[110px] sm:max-w-[180px] shrink-1 text-[11px] sm:text-xs" x-text="file.name"></span>
                                                <button type="button" @click="attachedFiles.splice(idx, 1)" class="text-slate-400 hover:text-rose-600 font-bold ml-1 cursor-pointer shrink-0">&times;</button>
                                            </div>
                                        </template>
                                    </div>

                                    <textarea name="appeal_message" required aria-label="Appeal explanation message" rows="3" placeholder="Explain why your account should be reinstated or detail corrected documents/details..." class="w-full bg-transparent text-xs sm:text-sm font-medium text-slate-900 outline-none border-none resize-none p-0 focus:ring-0 leading-relaxed"></textarea>

                                    <div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
                                        <button type="button" @click="$refs.appealFileInput.click()" title="Attach document or proof file" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200/80 hover:bg-blue-600 hover:text-white text-slate-700 text-xs font-black transition cursor-pointer border-none group">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span>Attach Proof</span>
                                        </button>

                                        <span class="text-[10px] text-slate-400 font-medium">Images, PDF (Max 10MB)</span>

                                        <input x-ref="appealFileInput" type="file" name="appeal_attachments[]" multiple accept="image/*,.pdf,.doc,.docx" class="hidden" aria-label="Appeal file attachments" @change="attachedFiles = Array.from($event.target.files)">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-6 rounded-2xl font-black text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md transition active:scale-98 cursor-pointer border-none uppercase tracking-wider">
                                Submit Appeal to TODA Admin
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    @endif
    
   <script>
    function updateFileName(input, type) {
        const file = input.files[0];
        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.webp|\.gif|\.bmp|\.tiff|\.tif|\.heic|\.heif|\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.odt|\.txt|\.rtf|\.csv)$/i;
        
        const textElement = document.getElementById(type + '_filename');
        const boxElement = document.getElementById(type + '_box');
        const iconElement = document.getElementById(type + '_icon');

        input.setCustomValidity("");

        if (file) {
            if (!allowedExtensions.exec(input.value)) {
                input.setCustomValidity("Invalid format. Please upload a JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, PDF, DOC, XLS, PPT, TXT, RTF, or CSV file.");
                textElement.textContent = "Invalid Format!";
                textElement.classList.replace('text-gray-500', 'text-red-500');
                boxElement.classList.replace('border-gray-300', 'border-red-500');
                iconElement.textContent = '❌';
                
                input.reportValidity();
                return;
            }

            if (file.size > 20 * 1024 * 1024) {
                input.setCustomValidity("File too large. Maximum size is 20MB.");
                textElement.textContent = "Too Large (Max 20MB)!";
                textElement.classList.replace('text-gray-500', 'text-red-500');
                boxElement.classList.replace('border-gray-300', 'border-red-500');
                iconElement.textContent = '❌';
                
                input.reportValidity();
                return;
            }

            textElement.textContent = file.name;
            textElement.classList.remove('text-gray-500', 'text-red-500');
            textElement.classList.add('text-green-600', 'font-bold');
            
            boxElement.classList.remove('border-gray-300', 'border-red-500');
            boxElement.classList.add('border-green-500', 'bg-green-50/50');
            
            iconElement.textContent = '✅';
        }
    }
</script>

    @if(session('trip_completed_modal') || (session('status') && str_contains(session('status'), 'Trip Completed')))
        @php
            session()->forget('trip_completed_modal');
        @endphp
        <div id="driver-trip-completed-modal" class="fixed inset-0 flex items-center justify-center p-4" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999999 !important; background-color: rgba(15, 23, 42, 0.85) !important; backdrop-filter: blur(12px);">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4 relative z-[10000000]">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto border-2 border-emerald-300 shadow-md">
                    ✓
                </div>
                <div>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                        Trip Completed
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 mt-2">Great Job!</h3>
                    <p class="text-xs font-bold text-slate-500 mt-1">Trip successfully completed &amp; fare collected.</p>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block mb-0.5">Collected Fare</span>
                    <span class="text-3xl font-black text-emerald-600">₱{{ session('completed_trip_fare') ? number_format(session('completed_trip_fare'), 2) : '30.00' }}</span>
                </div>

                @if($driver)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 flex items-center justify-between">
                    <span>New Position in Queue</span>
                    <span class="font-black text-blue-600">#{{ $driver->queue_position ?? 1 }}</span>
                </div>
                @endif

                <button type="button" onclick="document.getElementById('driver-trip-completed-modal').remove()" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider text-white shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-transform cursor-pointer" style="background-color: #10b981 !important; color: #ffffff !important; border: none;">
                    Back to Queue Duty
                </button>
            </div>
        </div>
    @endif
</x-app-layout>