<section class="space-y-6">
    <x-danger-button
        type="button"
        onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')"
    >
        {{ __('Delete Account') }}
    </x-danger-button>

    <div
        id="deleteAccountModal"
        class="hidden fixed inset-0 z-[9999] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <form
            id="deleteForm"
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl"
        >
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-2 text-sm text-gray-600 leading-6">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="delete_password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="delete_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Password') }}"
                />
            </div>

            <div id="deleteMessage" class="hidden mt-4"></div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button
                    type="button"
                    onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                >
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button id="deleteBtn" type="submit">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</section>