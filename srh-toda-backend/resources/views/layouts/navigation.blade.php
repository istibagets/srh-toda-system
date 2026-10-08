<style>
@media (max-width: 639px) {
    .desktop-nav-tabs {
        display: none !important;
    }
    .nav-notification-dropdown {
        position: fixed !important;
        left: 0.75rem !important;
        right: 0.75rem !important;
        top: calc(4.5rem + env(safe-area-inset-top, 0px)) !important;
        width: auto !important;
        max-width: calc(100vw - 1.5rem) !important;
        margin: 0 auto !important;
        z-index: 9999 !important;
    }
}
@media (min-width: 640px) {
    .desktop-nav-tabs {
        display: flex !important;
    }
    .nav-notification-dropdown {
        position: absolute !important;
        left: auto !important;
        right: 0 !important;
        top: 100% !important;
        margin-top: 0.5rem !important;
        width: 380px !important;
        z-index: 9999 !important;
    }
}
</style>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm pt-safe hidden sm:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 min-w-0 gap-2 sm:gap-4" style="display: flex; justify-content: space-between; align-items: center; height: 4rem; min-width: 0;">
            
            <!-- Left Branding & Nav Pills -->
            <div class="flex items-center min-w-0 shrink" style="display: flex; align-items: center; min-width: 0; flex-shrink: 1;">
                
                <!-- Logo & Title -->
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem; text-decoration: none; flex-shrink: 0;">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white flex items-center justify-center p-0.5 border border-slate-200/80 shadow-xs shrink-0" style="width: 44px; height: 44px; border-radius: 0.75rem; background-color: #ffffff; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; flex-shrink: 0;">
                        <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" width="38" height="38" class="w-9 h-9 sm:w-10 sm:h-10 object-contain" style="width: 38px; height: 38px; max-width: 38px; max-height: 38px; object-fit: contain; display: block;">
                    </div>
                    <span class="font-black text-lg sm:text-xl tracking-tight text-gray-800 shrink-0 whitespace-nowrap" style="font-weight: 900; font-size: 1.25rem; color: #1e293b; letter-spacing: -0.025em; white-space: nowrap; flex-shrink: 0;">{{ srh_setting('ui.nav_brand', 'SRH LINK TODA') }}</span>
                </a>

                <!-- Desktop Navigation Links Pills (Disappears on small mobile screens) -->
                <div class="desktop-nav-tabs hidden sm:flex items-center gap-1 md:gap-2 ms-2 md:ms-4 lg:ms-8 shrink-0" style="align-items: center; gap: 0.375rem; flex-shrink: 0;">
                    
                    <!-- HOME -->
                    <a href="{{ route('dashboard') }}" 
                       class="desktop-nav-link shrink-0 inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-xl text-xs lg:text-sm font-bold transition-all duration-150 active:scale-95 whitespace-nowrap {{ (request()->routeIs('dashboard') || request()->is('dashboard')) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 active-nav-tab' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-100' }}"
                       style="{{ (request()->routeIs('dashboard') || request()->is('dashboard')) ? 'background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;' : 'color: #475569 !important; background-color: transparent !important; box-shadow: none !important;' }} align-items: center; gap: 0.4rem; padding: 0.4rem 0.75rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; flex-shrink: 0; white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>

                    <!-- SAVED PLACES (Passengers) -->
                    <a href="{{ route('saved-locations.index') }}" 
                       class="desktop-nav-link shrink-0 inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-xl text-xs lg:text-sm font-bold transition-all duration-150 active:scale-95 whitespace-nowrap {{ (request()->routeIs('saved-locations.*') || request()->is('saved-locations*')) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 active-nav-tab' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-100' }}"
                       style="{{ (request()->routeIs('saved-locations.*') || request()->is('saved-locations*')) ? 'background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;' : 'color: #475569 !important; background-color: transparent !important; box-shadow: none !important;' }} align-items: center; gap: 0.4rem; padding: 0.4rem 0.75rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; flex-shrink: 0; white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span>Saved</span>
                    </a>

                    <!-- HISTORY -->
                    <a href="{{ route('history') }}" 
                       class="desktop-nav-link shrink-0 inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-xl text-xs lg:text-sm font-bold transition-all duration-150 active:scale-95 whitespace-nowrap {{ (request()->routeIs('history') || request()->is('history*')) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 active-nav-tab' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-100' }}"
                       style="{{ (request()->routeIs('history') || request()->is('history*')) ? 'background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;' : 'color: #475569 !important; background-color: transparent !important; box-shadow: none !important;' }} align-items: center; gap: 0.4rem; padding: 0.4rem 0.75rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; flex-shrink: 0; white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>History</span>
                    </a>

                    <!-- EARNINGS (Drivers & Admins Only) -->
                    @if(Auth::check() && (Auth::user()->role === 'driver' || Auth::user()->role === 'admin'))
                    <a href="{{ route('earnings') }}" 
                       class="desktop-nav-link shrink-0 inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-xl text-xs lg:text-sm font-bold transition-all duration-150 active:scale-95 whitespace-nowrap {{ (request()->routeIs('earnings') || request()->is('earnings*')) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 active-nav-tab' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-100' }}"
                       style="{{ (request()->routeIs('earnings') || request()->is('earnings*')) ? 'background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;' : 'color: #475569 !important; background-color: transparent !important; box-shadow: none !important;' }} align-items: center; gap: 0.4rem; padding: 0.4rem 0.75rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; flex-shrink: 0; white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08-.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Earnings</span>
                    </a>
                    @endif

                    <!-- ADMIN (Admins Only) -->
                    @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('drivers.index') }}" 
                       class="desktop-nav-link shrink-0 inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-xl text-xs lg:text-sm font-bold transition-all duration-150 active:scale-95 whitespace-nowrap {{ (request()->routeIs('drivers.index') || request()->is('drivers*') || request()->is('admin*')) ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 active-nav-tab' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-100' }}"
                       style="{{ (request()->routeIs('drivers.index') || request()->is('drivers*') || request()->is('admin*')) ? 'background-color: #2563eb !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;' : 'color: #475569 !important; background-color: transparent !important; box-shadow: none !important;' }} align-items: center; gap: 0.4rem; padding: 0.4rem 0.75rem; border-radius: 0.75rem; font-weight: 700; text-decoration: none; flex-shrink: 0; white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Admin</span>
                    </a>
                    @endif

                </div>
            </div>

            <!-- NOTIFICATIONS & PROFILE PILL (RIGHT) -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0" x-data="notificationSystem()" @srh-close-notifications.window="open = false" style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                
                <!-- Notification Bell Button -->
                <div class="relative shrink-0" style="position: relative; flex-shrink: 0;">
                    <button @click.stop="toggleDropdown()" type="button" class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-full transition-all focus:outline-none shrink-0" title="Notifications" style="padding: 0.5rem; border-radius: 9999px; cursor: pointer; border: none; background: transparent; flex-shrink: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" style="width: 24px; height: 24px; color: #334155;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        <!-- Unread Red Counter Badge -->
                        <template x-if="unreadCount > 0">
                            <span class="absolute top-1 right-1 w-4 h-4 bg-red-600 text-white font-black text-[9px] rounded-full flex items-center justify-center border-2 border-white animate-pulse" 
                                  style="position: absolute; top: 0px; right: 0px; width: 18px; height: 18px; background-color: #dc2626; color: #ffffff; font-weight: 900; font-size: 10px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff;"
                                  x-text="unreadCount"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown Menu -->
                     <div x-show="open" @click.outside="open = false" 
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                          x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                          x-transition:leave="transition ease-in duration-150"
                          x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                          x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                          class="nav-notification-dropdown bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden" 
                          style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.25); overflow: hidden;"
                          x-cloak>
                        
                        <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between" style="padding: 0.875rem 1rem; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                            <div class="flex items-center gap-2" style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="font-black text-sm text-gray-900 uppercase tracking-tight" style="font-weight: 900; font-size: 0.875rem; color: #0f172a;">Announcements</span>
                                <template x-if="unreadCount > 0">
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 font-black text-[10px] rounded-full" style="padding: 0.125rem 0.5rem; background-color: #fee2e2; color: #b91c1c; font-weight: 900; font-size: 10px; border-radius: 9999px;" x-text="unreadCount + ' new'"></span>
                                </template>
                            </div>

                            <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 0.75rem;">
                                <button @click="markAllAsRead()" type="button" class="text-xs font-bold text-blue-600 hover:text-blue-800 underline" style="font-size: 0.75rem; font-weight: 700; color: #2563eb; background: none; border: none; cursor: pointer;">
                                    Mark as read
                                </button>
                                @if(Auth::check() && Auth::user()->role === 'admin')
                                    <button @click="clearAllAnnouncements()" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 underline" style="font-size: 0.75rem; font-weight: 700; color: #dc2626; background: none; border: none; cursor: pointer;" title="Clear All Announcements">
                                        Clear All
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-80 sm:max-h-96 overflow-y-auto divide-y-2 divide-slate-200" style="max-height: 20rem; overflow-y: auto;">
                            <template x-if="announcements.length === 0">
                                <div class="p-8 text-center text-gray-400 text-xs italic" style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.75rem; font-style: italic;">
                                    No announcements at this time.
                                </div>
                            </template>

                            <template x-for="item in announcements" :key="item.id">
                                <div @click="handleNotificationClick(item)" class="p-4 transition-colors border-b-2 border-slate-200 relative cursor-pointer hover:bg-blue-50/80" :style="item.is_read ? 'background-color: #eff6ff;' : 'background-color: #ffffff;'" style="padding: 1rem; border-bottom: 2px solid #cbd5e1; cursor: pointer;">
                                    <div class="flex items-center justify-between mb-1.5" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.375rem;">
                                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md"
                                              :style="item.target_audience === 'RATING' ? 'background-color: #fef3c7; color: #b45309;' : (item.target_audience === 'APPEAL' ? 'background-color: #fee2e2; color: #991b1b;' : (item.target_audience === 'DRIVERS' ? 'background-color: #ffedd5; color: #c2410c;' : (item.target_audience === 'PASSENGERS' ? 'background-color: #d1fae5; color: #047857;' : 'background-color: #dbeafe; color: #1d4ed8;')))"
                                              style="font-size: 10px; font-weight: 900; padding: 0.125rem 0.5rem; border-radius: 0.375rem;"
                                              x-text="item.target_audience"></span>

                                        <div class="flex items-center gap-2" style="display: flex; align-items: center; gap: 0.5rem;" @click.stop>
                                            <template x-if="!item.is_read">
                                                <button @click.stop="markSingleAsRead(item)" type="button" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 underline cursor-pointer" title="Mark as read" style="font-size: 11px; font-weight: 700; color: #2563eb; background: none; border: none; cursor: pointer;">
                                                    Mark read
                                                </button>
                                            </template>

                                            <span class="text-[11px] font-bold text-gray-400" style="font-size: 11px; color: #94a3b8;" x-text="item.created_at"></span>
                                            
                                            <!-- Admin Delete Single Announcement Button -->
                                            @if(Auth::check() && Auth::user()->role === 'admin')
                                                <button @click.stop="deleteAnnouncement(item.id)" type="button" class="text-xs text-red-500 hover:text-red-700 font-bold p-1 cursor-pointer" title="Delete Announcement" style="background: none; border: none;">
                                                    <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <h4 class="font-extrabold text-sm text-gray-900 leading-snug" style="font-weight: 800; font-size: 0.875rem; color: #0f172a;" x-text="item.title"></h4>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed whitespace-pre-line break-words" style="font-size: 0.8125rem; color: #475569; margin-top: 0.25rem; word-break: break-word;" x-text="item.message"></p>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                <!-- Profile Avatar Link -->
                <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-2 px-2 sm:px-3 py-1.5 rounded-full hover:bg-gray-100 transition-all focus:outline-none shrink-0 min-w-0" title="Profile" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.375rem 0.625rem; border-radius: 9999px; flex-shrink: 0;">
                    <span class="hidden md:inline-block font-bold text-xs lg:text-sm text-gray-700 truncate max-w-[100px] md:max-w-[140px] lg:max-w-[200px] whitespace-nowrap" style="font-size: 0.875rem; font-weight: 700; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">{{ Auth::user()->name }}</span>
                    <div class="relative shrink-0" style="position: relative; flex-shrink: 0;">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center border-2 border-white shadow-md overflow-hidden shrink-0" style="width: 36px; height: 36px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb, #60a5fa); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            @if(Auth::user()->profile_photo_url)
                                <img id="nav-avatar-img" src="{{ route('user.avatar', [Auth::user(), 'v' => optional(Auth::user()->updated_at)->timestamp]) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover rounded-full" style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span id="nav-avatar-placeholder" class="font-black text-white text-base leading-none uppercase" style="color: #ffffff; font-weight: 900; font-size: 0.95rem; display: none;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @else
                                <img id="nav-avatar-img" src="" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover rounded-full hidden" style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span id="nav-avatar-placeholder" class="font-black text-white text-base leading-none uppercase" style="color: #ffffff; font-weight: 900; font-size: 0.95rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full" style="width: 10px; height: 10px; background-color: #22c55e; border: 2px solid #ffffff; border-radius: 9999px; position: absolute; bottom: 0; right: 0;"></div>
                    </div>
                </a>

            </div>

            <script>
                window._srhNotifFetchPromise = null;
                function _srhFetchNotificationsGlobal() {
                    if (window._srhNotifFetchPromise) return window._srhNotifFetchPromise;
                    window._srhNotifFetchPromise = fetch("{{ route('notifications.fetch') }}", {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.ok ? res.json() : null)
                    .finally(() => {
                        setTimeout(() => { window._srhNotifFetchPromise = null; }, 4000);
                    });
                    return window._srhNotifFetchPromise;
                }

                function notificationSystem() {
                    return {
                        open: false,
                        unreadCount: 0,
                        announcements: [],
                        init() {
                            if (window._srhNavNotificationTimer) {
                                clearInterval(window._srhNavNotificationTimer);
                                window._srhNavNotificationTimer = null;
                            }
                            window.addEventListener('srh-close-notifications', () => {
                                this.open = false;
                            });
                            window.closeAnnouncementModal = () => {
                                this.open = false;
                                document.body.style.overflow = '';
                                document.querySelectorAll('#announcementModal').forEach((el) => el.classList.add('hidden'));
                                window.dispatchEvent(new CustomEvent('srh-close-notifications'));
                            };
                        },
                        toggleDropdown() {
                            this.open = !this.open;
                            if (this.open) {
                                this.loadNotifications();
                            }
                        },
                        loadNotifications() {
                            _srhFetchNotificationsGlobal()
                                .then(data => {
                                    if (!data) return;
                                    const prevCount = this.unreadCount || 0;
                                    this.unreadCount = data.unread_count;

                                    if (this._hasLoaded && data.unread_count > prevCount && Array.isArray(data.announcements)) {
                                        const latest = data.announcements.find(a => !a.is_read);
                                        if (latest && typeof window.srhTriggerSystemNotification === 'function') {
                                            // Skip if the server push already showed this exact announcement banner
                                            if (window.srhShouldSuppressSystemNotification && window.srhShouldSuppressSystemNotification(latest.title, latest.message)) return;
                                            window.srhTriggerSystemNotification(latest.title, latest.message, { tag: 'announcement-' + latest.id });
                                        }
                                    }
                                    this._hasLoaded = true;

                                    this.announcements = data.announcements;
                                    if (data.user_profile) {
                                        this.syncProfileAvatar(data.user_profile);
                                    }
                                })
                                .catch(err => {});
                        },
                        syncProfileAvatar(profile) {
                            const navImg = document.getElementById('nav-avatar-img');
                            const navPlaceholder = document.getElementById('nav-avatar-placeholder');

                            if (profile.has_photo && profile.avatar_url) {
                                if (navImg) {
                                    if (navImg.src !== profile.avatar_url) {
                                        navImg.src = profile.avatar_url;
                                    }
                                    navImg.style.display = 'block';
                                    navImg.classList.remove('hidden');
                                }
                                if (navPlaceholder) {
                                    navPlaceholder.style.display = 'none';
                                }
                            } else {
                                if (navImg) {
                                    navImg.src = '';
                                    navImg.style.display = 'none';
                                    navImg.classList.add('hidden');
                                }
                                if (navPlaceholder) {
                                    navPlaceholder.style.display = 'flex';
                                    navPlaceholder.classList.remove('hidden');
                                }
                            }

                            // Sync page avatar if profile form is currently on screen
                            const pageImg = document.getElementById('photo-preview');
                            const pagePlaceholder = document.getElementById('photo-preview-placeholder');
                            const removeBtn = document.getElementById('remove-photo-btn');

                            // Skip overriding if user has a pending local file preview selected
                            if (pageImg && pageImg.dataset.userSelected === "true") {
                                return;
                            }

                            if (pageImg && pagePlaceholder) {
                                if (profile.has_photo && profile.avatar_url) {
                                    if (pageImg.src !== profile.avatar_url) {
                                        pageImg.src = profile.avatar_url;
                                    }
                                    pageImg.classList.remove('hidden');
                                    pageImg.style.display = 'block';
                                    pagePlaceholder.classList.add('hidden');
                                    if (removeBtn) removeBtn.classList.remove('hidden');
                                } else {
                                    pageImg.src = '';
                                    pageImg.classList.add('hidden');
                                    pageImg.style.display = 'none';
                                    pagePlaceholder.classList.remove('hidden');
                                    if (removeBtn) removeBtn.classList.add('hidden');
                                }
                            }
                        },
                        handleNotificationClick(item) {
                            this.markSingleAsRead(item);
                            if (item.action_url) {
                                this.open = false;
                                if (window.clearPageCache) window.clearPageCache();
                                if (window.navigateTo) {
                                    window.navigateTo(item.action_url, false, false, true);
                                } else {
                                    window.location.href = item.action_url;
                                }
                            }
                        },
                        markSingleAsRead(item) {
                            if (!item || item.is_read) return;
                            item.is_read = true;
                            if (this.unreadCount > 0) this.unreadCount--;

                            fetch("{{ route('notifications.mark-read') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Content-Type": "application/json",
                                    "Accept": "application/json"
                                },
                                body: JSON.stringify({ id: item.id })
                            }).catch(err => {});
                        },
                        markAsRead(target) {
                            if (!target) return;
                            if (typeof target === 'object') {
                                this.markSingleAsRead(target);
                            } else {
                                const item = (this.announcements || []).find(a => a.id === target);
                                if (item) this.markSingleAsRead(item);
                            }
                        },
                        markAllAsRead() {
                            fetch("{{ route('notifications.mark-read') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            })
                            .then(res => res.json())
                            .then(() => {
                                this.unreadCount = 0;
                                this.announcements.forEach(a => a.is_read = true);
                            });
                        },
                        confirmDeleteModalOpen: false,
                        deleteTargetId: null,
                        promptDeleteAnnouncement(id) {
                            this.deleteTargetId = id;
                            this.confirmDeleteModalOpen = true;
                            this.open = true;
                        },
                        cancelDeleteAnnouncement() {
                            this.confirmDeleteModalOpen = false;
                            this.deleteTargetId = null;
                            this.open = true;
                        },
                        confirmDeleteAnnouncement() {
                            if (!this.deleteTargetId) return;
                            const id = this.deleteTargetId;
                            this.confirmDeleteModalOpen = false;
                            this.deleteTargetId = null;
                            this.open = true;

                            fetch("/announcements/" + id, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            })
                            .then(res => res.json())
                            .then(() => {
                                if (window.createSlidingToast) window.createSlidingToast('Announcement deleted.', 'success');
                                this.loadNotifications();
                                this.open = true;
                            })
                            .catch(err => {
                                if (window.createSlidingToast) window.createSlidingToast('Failed to delete announcement.', 'error');
                                this.open = true;
                            });
                        },
                        deleteAnnouncement(id) {
                            this.promptDeleteAnnouncement(id);
                        },
                        clearAllAnnouncements() {
                            if (!confirm('Are you sure you want to clear all announcements?')) return;
                            fetch("{{ route('notifications.clear-all') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            })
                            .then(res => res.json())
                            .then(() => {
                                this.unreadCount = 0;
                                this.announcements = [];
                            });
                        }
                    }
                }
            </script>

        </div>
    </div>
</nav>