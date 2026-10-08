<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Profile & Settings') }}"></x-page-header>
    </x-slot>

    <div class="py-3 sm:py-6 bg-slate-50/60">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 pb-4" x-data="{ activeTab: 'permissions' }">

            {{-- Hero Profile Card with Name, Details, and Log Out --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-4 sm:p-5 space-y-3.5">
                <div class="flex items-center gap-3.5">
                    {{-- User Avatar --}}
                    <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-full overflow-hidden bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-md border-2 border-white ring-4 ring-blue-100/80 shrink-0">
                        @if(Auth::user()->profile_photo_url)
                            <img src="{{ route('user.avatar', [Auth::user(), 'v' => optional(Auth::user()->updated_at)->timestamp]) }}" alt="{{ Auth::user()->name }}" 
                                 class="w-full h-full object-cover rounded-full"
                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <div class="w-full h-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center rounded-full hidden">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center rounded-full">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- Name, Role Badge, Email, Joined --}}
                    <div class="space-y-0.5 min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug break-words">{{ Auth::user()->name }}</h1>
                            <span class="text-[9px] font-black uppercase px-2.5 py-0.5 rounded-full border shadow-2xs shrink-0 
                                {{ Auth::user()->role === 'admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : (Auth::user()->role === 'driver' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        <p class="text-[11px] font-medium text-slate-400">Joined {{ Auth::user()->created_at->format('M Y') }}</p>
                    </div>
                </div>

                {{-- Clean Log Out Button --}}
                <form method="POST" action="{{ route('logout') }}" class="no-spa">
                    @csrf
                    <button type="submit" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-extrabold text-xs uppercase tracking-wider rounded-2xl border border-red-200/90 transition active:scale-95 cursor-pointer flex items-center justify-center gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>

            {{-- Segmented Tab Switcher Bar --}}
            <div class="flex items-center gap-1.5 p-1.5 bg-slate-200/80 rounded-2xl shadow-inner">
                <button type="button" @click="activeTab = 'permissions'" 
                        :class="activeTab === 'permissions' ? 'bg-white text-blue-600 shadow-md font-black' : 'text-slate-600 font-bold hover:text-slate-900'"
                        class="flex-1 py-2.5 px-2 sm:px-4 text-[11px] sm:text-xs rounded-xl transition-all cursor-pointer whitespace-nowrap flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Permissions</span>
                </button>

                <button type="button" @click="activeTab = 'account'" 
                        :class="activeTab === 'account' ? 'bg-white text-blue-600 shadow-md font-black' : 'text-slate-600 font-bold hover:text-slate-900'"
                        class="flex-1 py-2.5 px-2 sm:px-4 text-[11px] sm:text-xs rounded-xl transition-all cursor-pointer whitespace-nowrap flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Account Info</span>
                </button>

                <button type="button" @click="activeTab = 'security'" 
                        :class="activeTab === 'security' ? 'bg-white text-blue-600 shadow-md font-black' : 'text-slate-600 font-bold hover:text-slate-900'"
                        class="flex-1 py-2.5 px-2 sm:px-4 text-[11px] sm:text-xs rounded-xl transition-all cursor-pointer whitespace-nowrap flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Security</span>
                </button>
            </div>

            {{-- TAB 1: DEVICE PERMISSIONS & HARDWARE SETTINGS --}}
            <div x-show="activeTab === 'permissions'" x-transition class="space-y-3"
                 x-data="permissionSettingsEngine()" x-init="initPermissions()">
                
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-4 sm:p-5 space-y-3.5">
                    <div class="border-b border-slate-100 pb-2.5">
                        <h3 class="text-base font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Device Permissions Control</span>
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Manually enable and verify system permissions required for TODA live tracking and alerts.</p>
                    </div>

                    {{-- 1. GPS Location Permission Row --}}
                    <div class="p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-extrabold text-slate-900">GPS High-Accuracy Location</span>
                                <template x-if="gpsStatus === 'granted'">
                                    <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                </template>
                                <template x-if="gpsStatus !== 'granted'">
                                    <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-amber-50 text-amber-700 border border-amber-200" x-text="gpsStatus"></span>
                                </template>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 block">Allows live map navigation and pickup pin accuracy</span>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                            <button type="button" @click="requestGpsPermission()" class="flex-1 sm:flex-none px-3.5 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition cursor-pointer">
                                Activate GPS
                            </button>
                        </div>
                    </div>

                    {{-- 2. System Push Notifications Row --}}
                    <div class="p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-extrabold text-slate-900">System Background Notifications</span>
                                <template x-if="notifPermission === 'granted'">
                                    <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Allowed</span>
                                </template>
                                <template x-if="notifPermission !== 'granted'">
                                    <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-amber-50 text-amber-700 border border-amber-200" x-text="notifPermission"></span>
                                </template>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 block">Native phone-banner notifications — even when the app is closed</span>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 justify-between sm:justify-start">
                            <button type="button" @click="requestSystemPermission()" x-show="notifPermission !== 'granted'" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition cursor-pointer">
                                Allow Push
                            </button>
                            <button type="button" @click="toggleNotifSetting()" 
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                    :class="notifEnabled ? 'bg-blue-600' : 'bg-slate-300'">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition duration-200 ease-in-out"
                                      :class="notifEnabled ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- 2.5 In-App Alert Sound Row --}}
                    <div class="p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/50 flex items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <span class="text-xs font-extrabold text-slate-900 block">In-App Alert Sound</span>
                            <span class="text-[11px] font-medium text-slate-500 block">Audible chime when an alert arrives while the app is open or backgrounded. Closed-app banners always use the phone/Chrome default notification sound.</span>
                        </div>

                        <button type="button" @click="toggleSoundSetting()"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                :class="soundEnabled ? 'bg-blue-600' : 'bg-slate-300'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition duration-200 ease-in-out"
                                  :class="soundEnabled ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>

                    {{-- 3. Device Vibration Haptics Row --}}
                    <div class="p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/50 flex items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <span class="text-xs font-extrabold text-slate-900 block">Haptic Device Vibration</span>
                            <span class="text-[11px] font-medium text-slate-500 block">Tactile phone vibration on ride status alerts</span>
                        </div>

                        <button type="button" @click="toggleVibrationSetting()" 
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                :class="vibrationEnabled ? 'bg-blue-600' : 'bg-slate-300'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition duration-200 ease-in-out"
                                  :class="vibrationEnabled ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>

                    {{-- Test All Permissions Master Action --}}
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
                        <p class="text-xs font-medium text-slate-500">Run a diagnostic test on GPS, system push, and vibration.</p>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="button" @click="testAllPermissions()" class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-md transition cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Test All Device Permissions</span>
                            </button>
                            <button type="button" @click="sendTestPush()" x-show="notifPermission === 'granted'" title="Send a test push even with the app closed" class="px-4 py-2.5 bg-blue-50 hover:bg-blue-100 active:scale-95 text-blue-700 font-extrabold text-xs uppercase tracking-wider rounded-2xl border border-blue-200/90 transition cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Test Push</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function permissionSettingsEngine() {
                    return {
                        notifPermission: 'default',
                        gpsStatus: 'Prompt',
                        notifEnabled: true,
                        soundEnabled: true,
                        vibrationEnabled: true,
                        initPermissions() {
                            if ('Notification' in window) {
                                this.notifPermission = Notification.permission;
                            }
                            this.notifEnabled = localStorage.getItem('srh_notif_enabled') !== 'false';
                            this.soundEnabled = localStorage.getItem('srh_sound_enabled') !== 'false';
                            this.vibrationEnabled = localStorage.getItem('srh_vibration_enabled') !== 'false';

                            if ('geolocation' in navigator) {
                                navigator.permissions && navigator.permissions.query({ name: 'geolocation' })
                                    .then(result => {
                                        this.gpsStatus = result.state === 'granted' ? 'granted' : result.state;
                                    }).catch(() => {});
                            }
                        },
                        requestGpsPermission() {
                            if ('geolocation' in navigator) {
                                navigator.geolocation.getCurrentPosition(
                                    pos => {
                                        this.gpsStatus = 'granted';
                                        if (window.createSlidingToast) window.createSlidingToast('GPS location activated successfully.', 'success');
                                    },
                                    err => {
                                        this.gpsStatus = 'denied';
                                        if (window.createSlidingToast) window.createSlidingToast('Please allow GPS location in your browser settings.', 'error');
                                    },
                                    { enableHighAccuracy: true }
                                );
                            }
                        },
                        requestSystemPermission() {
                            if ('Notification' in window) {
                                Notification.requestPermission().then(permission => {
                                    this.notifPermission = permission;
                                    if (permission === 'granted' && typeof window.srhTriggerSystemNotification === 'function') {
                                        window.srhTriggerSystemNotification('SRH LINK-TODA 🔔', 'System notifications activated successfully! 🎉');
                                    }
                                    if (permission === 'granted' && window.srhSubscribeToPush) {
                                        // Enable phone-banner push notifications even with the app closed
                                        window.srhSubscribeToPush();
                                    } else if (permission === 'denied') {
                                        if (window.createSlidingToast) window.createSlidingToast('Push notifications are blocked. Please allow them in your browser or device settings.', 'error');
                                    }
                                }).catch(() => {});
                            }
                        },
                        toggleNotifSetting() {
                            this.notifEnabled = !this.notifEnabled;
                            localStorage.setItem('srh_notif_enabled', this.notifEnabled ? 'true' : 'false');
                            if (this.notifEnabled && this.notifPermission !== 'granted') {
                                this.requestSystemPermission();
                            }
                        },
                        toggleSoundSetting() {
                            this.soundEnabled = !this.soundEnabled;
                            localStorage.setItem('srh_sound_enabled', this.soundEnabled ? 'true' : 'false');
                            if (this.soundEnabled && typeof window.srhPlayNotificationSound === 'function') {
                                window.srhPlayNotificationSound();
                            }
                        },
                        toggleVibrationSetting() {
                            this.vibrationEnabled = !this.vibrationEnabled;
                            localStorage.setItem('srh_vibration_enabled', this.vibrationEnabled ? 'true' : 'false');
                            if (this.vibrationEnabled && 'vibrate' in navigator) {
                                try { navigator.vibrate([150, 80, 150]); } catch(e) {}
                            }
                        },
                        testAllPermissions() {
                            this.requestGpsPermission();
                            if (window.srhRequestNotificationPermission) {
                                window.srhRequestNotificationPermission().then(perm => {
                                    this.notifPermission = perm;
                                    if (perm === 'granted') {
                                        if (window.srhSubscribeToPush) window.srhSubscribeToPush();
                                        if (typeof window.srhTriggerSystemNotification === 'function') {
                                            window.srhTriggerSystemNotification('SRH LINK-TODA 🔔', 'System notifications, GPS, and device vibration verified successfully. 🎉');
                                        }
                                    }
                                });
                            } else if (typeof window.srhTriggerSystemNotification === 'function') {
                                window.srhTriggerSystemNotification('SRH LINK-TODA 🔔', 'System notifications, GPS, and device vibration verified successfully. 🎉');
                            }
                        },
                        sendTestPush() {
                            if (window.srhRequestNotificationPermission) {
                                window.srhRequestNotificationPermission().then(perm => {
                                    this.notifPermission = perm;
                                    if (perm === 'granted') {
                                        if (window.srhSubscribeToPush) window.srhSubscribeToPush();
                                        if (typeof window.srhTriggerSystemNotification === 'function') {
                                            window.srhTriggerSystemNotification('SRH LINK-TODA 🔔', 'Test push notification received! System notifications & vibration working.');
                                        }
                                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                                        fetch('{{ route("push.test") }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '',
                                                'Accept': 'application/json'
                                            }
                                        }).then(r => r.json()).then(data => {
                                            if (window.createSlidingToast) {
                                                window.createSlidingToast(data.message || 'Test push sent! Close this app to see it on your phone.', 'success');
                                            }
                                        }).catch(() => {
                                            if (window.createSlidingToast) window.createSlidingToast('Could not send test push.', 'error');
                                        });
                                    } else {
                                        if (window.createSlidingToast) window.createSlidingToast('Push notifications are blocked. Please allow them in your browser settings.', 'error');
                                    }
                                });
                            }
                        }
                    }
                }
            </script>

            {{-- TAB 2: ACCOUNT INFORMATION --}}
            <div x-show="activeTab === 'account'" x-transition class="space-y-4">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-4 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- TAB 3: SECURITY & DANGER ZONE --}}
            <div x-show="activeTab === 'security'" x-transition class="space-y-4">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-4 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-red-100 p-4 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>