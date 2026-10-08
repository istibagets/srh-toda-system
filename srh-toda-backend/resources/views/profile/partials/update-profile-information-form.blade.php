<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-gray-900 tracking-tight">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm font-medium text-gray-500">
            {{ __("Update your account's profile information, profile picture, and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="profile-update-form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6 no-spa">
        @csrf
        @method('patch')

        <!-- CLEAN PROFILE PHOTO CONTAINER WITH DIRECT LABEL PICKER -->
        <div class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-50/60 via-white to-slate-50 rounded-3xl border border-blue-100/80 shadow-xs text-center space-y-4">
            
            <!-- 100px Circle Avatar wrapped in label for instant file selection -->
            <label for="profile_photo" class="relative group cursor-pointer block" title="Click to choose a photo" style="width: 100px; height: 100px;">
                <div class="w-25 h-25 rounded-full overflow-hidden bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center shadow-lg border-4 border-white ring-4 ring-blue-100/90"
                     style="width: 100px; height: 100px; min-width: 100px; min-height: 100px; max-width: 100px; max-height: 100px; border-radius: 9999px;">
                    @if($user->profile_photo_url)
                        <img id="photo-preview" src="{{ route('user.avatar', [$user, 'v' => optional($user->updated_at)->timestamp]) }}" alt="{{ $user->name }}" 
                             class="w-full h-full object-cover rounded-full" style="width: 100px; height: 100px; max-width: 100px; max-height: 100px; object-fit: cover;"
                             onerror="this.style.display='none'; document.getElementById('photo-preview-placeholder').classList.remove('hidden');">
                        
                        <div id="photo-preview-placeholder" class="w-full h-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center rounded-full hidden"
                             style="width: 100px; height: 100px; border-radius: 9999px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @else
                        <div id="photo-preview-placeholder" class="w-full h-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center rounded-full"
                             style="width: 100px; height: 100px; border-radius: 9999px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <img id="photo-preview" src="" alt="Preview" class="w-full h-full object-cover rounded-full hidden" style="width: 100px; height: 100px; max-width: 100px; max-height: 100px; object-fit: cover;">
                    @endif
                </div>

                <!-- Camera Action Badge -->
                <div class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 group-hover:bg-blue-700 active:scale-90 text-white rounded-full flex items-center justify-center shadow-md border-2 border-white transition-all transform group-hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </label>

            <!-- Hidden Inputs -->
            <input id="profile_photo" name="profile_photo" type="file" accept="image/*" onchange="previewProfilePhoto(event)" class="hidden">
            <input id="remove_photo" name="remove_photo" type="hidden" value="0">

            <!-- Selected File Indicator / Remove Button -->
            <div class="flex items-center gap-2">
                <span id="file-selected-badge" class="hidden text-xs font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                    New Photo Selected
                </span>

                @if($user->profile_photo_url)
                    <button type="button" id="remove-photo-btn" onclick="removeProfilePhoto()"
                            class="px-3.5 py-1.5 bg-white hover:bg-red-50 text-red-600 font-extrabold text-xs rounded-xl border border-red-200 shadow-2xs hover:border-red-300 transition active:scale-95 flex items-center gap-1.5 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Remove Photo</span>
                    </button>
                @endif
            </div>
            
            <x-input-error class="mt-1" :messages="$errors->get('profile_photo')" />
        </div>

        <!-- Name Input -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="font-bold text-xs uppercase tracking-wider text-gray-700" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-xs font-bold text-gray-900" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email Input -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-bold text-xs uppercase tracking-wider text-gray-700" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-xs font-bold text-gray-900" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4">
            <x-primary-button class="py-3 px-6 bg-blue-600 hover:bg-blue-700 rounded-xl font-black text-sm uppercase tracking-wider shadow-md shadow-blue-500/20 active:scale-95 transition-all cursor-pointer">{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-black text-emerald-600 flex items-center gap-1.5"
                >
                    <span>✅</span>
                    <span>{{ __('Profile updated successfully!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>

<script>
    function previewProfilePhoto(event) {
        const file = event.target.files[0];
        if (file) {
            // 10MB size limit check (10 * 1024 * 1024 bytes)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size exceeds the 10MB limit. Please select a smaller photo.');
                event.target.value = '';
                return;
            }

            document.getElementById('remove_photo').value = "0";

            const fileBadge = document.getElementById('file-selected-badge');
            if (fileBadge) {
                fileBadge.classList.remove('hidden');
            }

            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-preview-placeholder');
            
            if (preview) {
                // Lock preview state from background polling overrides
                preview.dataset.userSelected = "true";

                // Instant synchronous 0ms preview using ObjectURL
                try {
                    preview.src = URL.createObjectURL(file);
                } catch (err) {}

                preview.classList.remove('hidden');
                preview.style.display = 'block';
            }

            if (placeholder) {
                placeholder.classList.add('hidden');
                placeholder.style.display = 'none';
            }

            // Fallback reader for guaranteed data URL load
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    function removeProfilePhoto() {
        if (!confirm('Are you sure you want to remove your profile photo?')) return;

        document.getElementById('remove_photo').value = "1";
        document.getElementById('profile_photo').value = "";

        const preview = document.getElementById('photo-preview');
        const placeholder = document.getElementById('photo-preview-placeholder');
        const fileBadge = document.getElementById('file-selected-badge');

        if (preview) {
            preview.src = "";
            preview.classList.add('hidden');
            preview.style.display = 'none';
        }
        if (placeholder) {
            placeholder.classList.remove('hidden');
        }
        if (fileBadge) {
            fileBadge.classList.add('hidden');
        }

        // Submit form to save photo removal immediately
        document.getElementById('profile-update-form').submit();
    }
</script>
