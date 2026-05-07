<section>
    <form id="passwordForm" method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
        </div>

        <div id="passwordMessage" class="hidden"></div>

        <div class="flex items-center gap-4">
            <x-primary-button id="passwordSaveBtn" type="submit">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordForm = document.getElementById('passwordForm');

            if (!passwordForm) return;

            passwordForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const btn = document.getElementById('passwordSaveBtn');
                const messageBox = document.getElementById('passwordMessage');

                const currentPassword = document.getElementById('update_password_current_password').value;
                const password = document.getElementById('update_password_password').value;
                const passwordConfirmation = document.getElementById('update_password_password_confirmation').value;

                const originalText = btn.innerHTML;

                btn.disabled = true;
                btn.classList.add('opacity-60', 'cursor-not-allowed');

                btn.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    Saving...
                `;

                try {
                    const res = await fetch("{{ route('password.update') }}", {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            current_password: currentPassword,
                            password: password,
                            password_confirmation: passwordConfirmation
                        })
                    });

                    let data = {};

                    try {
                        data = await res.json();
                    } catch (e) {
                        data = {};
                    }

                    if (!res.ok) {
                        let errorText = 'Không thể đổi mật khẩu. Vui lòng kiểm tra lại.';

                        if (data.errors) {
                            errorText = Object.values(data.errors)
                                .flat()
                                .join('<br>');
                        }

                        messageBox.classList.remove('hidden');
                        messageBox.innerHTML = `
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                                    <div>${errorText}</div>
                                </div>
                            </div>
                        `;

                        return;
                    }

                    messageBox.classList.remove('hidden');
                    messageBox.innerHTML = `
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500"></i>
                                <div>Đổi mật khẩu thành công.</div>
                            </div>
                        </div>
                    `;

                    passwordForm.reset();

                } catch (error) {
                    console.error(error);

                    messageBox.classList.remove('hidden');
                    messageBox.innerHTML = `
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 shadow-sm">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                                <div>Có lỗi xảy ra. Vui lòng thử lại.</div>
                            </div>
                        </div>
                    `;
                } finally {
                    btn.disabled = false;
                    btn.classList.remove('opacity-60', 'cursor-not-allowed');
                    btn.innerHTML = originalText;
                }
            });
        });
    </script>
</section>