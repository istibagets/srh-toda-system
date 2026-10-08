<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-6">
        <div class="max-w-4xl w-full text-center space-y-8">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome to SRH LINK-TODA</h2>
            <p class="text-sm text-gray-500 font-medium max-w-md mx-auto">Please select your primary role to customize your TODA dashboard experience.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <form method="POST" action="{{ route('identity.store') }}" onsubmit="handleRideActionSubmit(event, this)">
                    @csrf
                    <input type="hidden" name="role" value="passenger">
                    <button type="submit" class="w-full bg-white border border-gray-100 p-8 rounded-3xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all cursor-pointer">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900">I am a Passenger</h3>
                        <p class="text-xs text-gray-500 font-medium mt-1">Book TODA rides and track trips in real-time</p>
                    </button>
                </form>

                <form method="POST" action="{{ route('identity.store') }}" onsubmit="handleRideActionSubmit(event, this)">
                    @csrf
                    <input type="hidden" name="role" value="driver">
                    <button type="submit" class="w-full bg-white border border-gray-100 p-8 rounded-3xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all cursor-pointer">
                        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900">I am a Driver</h3>
                        <p class="text-xs text-gray-500 font-medium mt-1">Manage queue status, accept rides & earn</p>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>