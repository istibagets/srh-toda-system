<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Earnings & Revenue') }}"></x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4 space-y-8">
            
            <!-- Summary Metric Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- TODAY'S EARNINGS -->
                <div class="bg-white rounded-3xl p-6 text-slate-900 shadow-xs border border-slate-200/80 relative overflow-hidden flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wider text-blue-700">Today's Earnings</p>
                            <h3 class="text-3xl font-extrabold mt-2 tracking-tight text-slate-900">₱{{ number_format($todayEarnings, 2) }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl border border-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08-.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Income accumulated today</p>
                </div>

                <!-- WEEKLY EARNINGS -->
                <div class="bg-white rounded-3xl p-6 text-gray-900 shadow-xl border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">This Week</p>
                            <h3 class="text-3xl font-black mt-2 text-gray-900 tracking-tight">₱{{ number_format($weeklyEarnings, 2) }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl border border-emerald-100">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Total weekly revenue</p>
                </div>

                <!-- MONTHLY EARNINGS -->
                <div class="bg-white rounded-3xl p-6 text-gray-900 shadow-xl border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">This Month</p>
                            <h3 class="text-3xl font-black mt-2 text-gray-900 tracking-tight">₱{{ number_format($monthlyEarnings, 2) }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl border border-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Total monthly revenue</p>
                </div>

                <!-- TOTAL COMPLETED TRIPS -->
                <div class="bg-white rounded-3xl p-6 text-gray-900 shadow-xl border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">Trips Completed</p>
                            <h3 class="text-3xl font-black mt-2 text-gray-900 tracking-tight">{{ $totalRides }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl border border-amber-100">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Total successful rides</p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
