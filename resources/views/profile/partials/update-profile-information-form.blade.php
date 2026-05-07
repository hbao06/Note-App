<section>
    <form
        id="profileInfoForm"
        method="POST"
        action="{{ route('profile.update') }}"
        class="space-y-6"
    >
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autocomplete="name"
            />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
        </div>

        <div id="profileInfoMessage" class="hidden"></div>

        <div class="flex items-center gap-4">
            <x-primary-button id="profileInfoSaveBtn" type="submit">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</section>

<script>
async function submitProfileInfo(event) {
    event.preventDefault();

    const form = document.getElementById('profileInfoForm');
    const btn = document.getElementById('profileInfoSaveBtn');
    const messageBox = document.getElementById('profileInfoMessage');

    const formData = new FormData(form);

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-not-allowed');
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...`;

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            let errorText = 'Không thể cập nhật thông tin.';

            if (data.errors) {
                errorText = Object.values(data.errors).flat().join('<br>');
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
                    <div>Cập nhật thông tin thành công.</div>
                </div>
            </div>
        `;

    } catch (error) {
        console.error(error);
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
        btn.innerHTML = originalText;
    }
}
</script>