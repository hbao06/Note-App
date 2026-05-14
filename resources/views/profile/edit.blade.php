<x-app-layout>
    @php
        $user = auth()->user();

        // Màu avatar mặc định: màu đậm, không có trắng
        $avatarColors = [
            '#334155',
            '#374151',
            '#3f3f46',
            '#44403c',
            '#dc2626',
            '#ea580c',
            '#d97706',
            '#65a30d',
            '#16a34a',
            '#059669',
            '#0d9488',
            '#0891b2',
            '#0284c7',
            '#2563eb',
            '#4f46e5',
            '#7c3aed',
            '#9333ea',
            '#c026d3',
            '#db2777',
            '#e11d48',
        ];

        $avatarColor = $avatarColors[abs(crc32($user->email)) % count($avatarColors)];
        $initial = strtoupper(mb_substr($user->name ?? 'U', 0, 1, 'UTF-8'));

        $hasAvatar = $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar);
    @endphp

    <div class="h-screen overflow-hidden bg-gray-50 text-gray-900">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed left-0 top-0 z-40 h-screen w-64 
                bg-white border-r border-gray-200 
                transition-all duration-300 ease-in-out 
                flex flex-col overflow-hidden">

            <!-- USER -->
            <div id="sidebarUser" class="p-4 border-b border-gray-200">
                <a href="{{ route('profile.edit') }}"
                    id="userInfo"
                    class="sidebar-avatar-link flex items-center gap-3 min-w-0 rounded-xl hover:bg-gray-100 transition">

                   <div id="userAvatar"
                        class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md"
                        style="background-color: {{ $hasAvatar ? '#e5e7eb' : $avatarColor }};">

                        @if ($hasAvatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                alt="Avatar"
                                class="w-full h-full object-cover">
                        @else
                            <span class="select-none">{{ $initial }}</span>
                        @endif
                    </div>

                    <div class="sidebar-text min-w-0">
                        <div class="font-semibold text-gray-900 truncate">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-xs text-gray-400 truncate">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </a>
            </div>

            <!-- MENU -->
            <nav class="p-3 space-y-2">

                <a href="{{ route('notes.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl transition text-gray-600 hover:bg-gray-100"
                    data-route="notes">
                    <i class="fa-solid fa-note-sticky w-5"></i>
                    <span class="sidebar-text">My Notes</span>
                </a>

                <a href="{{ route('notes.shared') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl transition text-gray-600 hover:bg-gray-100"
                    data-route="shared">

                    <i class="fa-solid fa-user-group w-5"></i>

                    <span class="sidebar-text flex-1">Shared with me</span>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="sidebar-text min-w-[22px] h-[22px] px-2 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>

                <button onclick="openSettingsModal()"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-gear w-5"></i>
                    <span class="sidebar-text">Settings</span>
                </button>

            </nav>

            <!-- RECENT NOTES -->
            <div class="px-4 mt-4 sidebar-text flex-1 min-h-0 flex flex-col">
                <h3 class="text-xs uppercase tracking-widest text-gray-400 mb-3 flex-shrink-0">
                    Gần đây
                </h3>

                <div class="space-y-2 overflow-y-auto pr-1 flex-1 min-h-0">
                    @isset($notes)
                        @foreach($notes->take(20) as $recent)
                            <a href="{{ url('/notes/editor/' . $recent->id) }}"
                                class="block px-3 py-2 rounded-xl hover:bg-gray-100 transition">
                                <div class="text-sm text-gray-800 truncate">
                                    {{ $recent->title ?: 'Untitled' }}
                                </div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ Str::limit($recent->content, 32) }}
                                </div>
                            </a>
                        @endforeach
                    @endisset
                </div>
            </div>

            <!-- LOGOUT -->
            <div class="mt-auto px-3 pb-4 w-full">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                        <i class="fa-solid fa-right-from-bracket w-5 flex-shrink-0"></i>
                        <span class="sidebar-text">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- TOGGLE SIDEBAR -->
        <button onclick="toggleSidebar()"
            id="sidebarToggleBtn"
            class="fixed top-8 z-50 
                w-9 h-9 flex items-center justify-center
                rounded-full bg-white/90 backdrop-blur
                border border-gray-200 shadow-sm
                text-gray-600 hover:scale-105 hover:shadow-md
                transition-all duration-200">

            <i id="sidebarToggleIcon" class="fa-solid fa-chevron-left text-xs"></i>
        </button>

        <div class="pointer-events-none fixed inset-0 bg-white"></div>

        <!-- MAIN CONTENT -->
        <div id="mainContent" class="relative h-screen overflow-hidden transition-all duration-300 ml-64 bg-gray-50">

            <div class="h-full overflow-y-auto bg-gray-50 text-gray-900 pb-28">
                <div class="max-w-4xl mx-auto px-6 py-10 space-y-6">

                    <!-- ALERTS -->
                    @if (session('status') === 'avatar-updated')
                        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
                            Cập nhật ảnh đại diện thành công.
                        </div>
                    @endif

                    @error('avatar')
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- HERO PROFILE -->
                    <div class="relative overflow-hidden rounded-[2rem] bg-white border border-gray-200 shadow-sm">
                        <div class="h-32 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-600"></div>

                        <div class="px-8 pb-8">
                            <div class="-mt-14 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
                                <div class="flex items-end gap-5">

                                    <!-- AVATAR HERO -->
                                    <div class="relative flex-shrink-0"
                                        style="width: 132px; height: 132px;">

                                        <!-- Vòng tròn lớn: avatar -->
                                        <div
                                            class="rounded-full overflow-hidden flex items-center justify-center text-white text-5xl font-bold ring-4 ring-white shadow-xl"
                                            style="width: 132px; height: 132px; background-color: {{ $hasAvatar ? '#e5e7eb' : $avatarColor }};">

                                            @if ($hasAvatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                                    alt="Avatar"
                                                    class="block object-cover"
                                                    style="width: 132px; height: 132px;">
                                            @else
                                                <span class="select-none leading-none">
                                                    {{ $initial }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Vòng tròn nhỏ: icon camera nằm đè lên avatar -->
                                        <label for="avatarInput"
                                            class="absolute z-20 rounded-full bg-gray-950 text-white flex items-center justify-center shadow-lg cursor-pointer hover:bg-gray-700 active:scale-95 transition ring-4 ring-white"
                                            style="width: 44px; height: 44px; right: 4px; bottom: 4px;"
                                            title="Thay đổi ảnh đại diện">
                                            <i class="fa-solid fa-camera text-sm"></i>
                                        </label>
                                    </div>

                                    <div class="pb-3">
                                        <h1 class="profile-hero-name text-3xl font-bold tracking-tight">
                                            {{ auth()->user()->name }}
                                        </h1>

                                        <p class="profile-hero-email mt-1 text-sm text-gray-500">
                                            {{ auth()->user()->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PROFILE INFO -->
                    <div class="bg-white border border-gray-200 rounded-[2rem] p-7 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-700">
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-bold">Thông tin cá nhân</h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Cập nhật tên hiển thị và email tài khoản.
                                </p>
                            </div>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div class="bg-white border border-gray-200 rounded-[2rem] p-7 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-700">
                                <i class="fa-solid fa-lock"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-bold">Đổi mật khẩu</h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Tăng bảo mật bằng mật khẩu mạnh và khó đoán.
                                </p>
                            </div>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- DELETE ACCOUNT -->
                    <div class="bg-white border border-red-100 rounded-[2rem] p-7 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center text-red-500">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>

                            <div>
                                <h2 class="text-xl font-bold text-red-600">Xóa tài khoản</h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Sau khi xóa, toàn bộ dữ liệu tài khoản sẽ không thể khôi phục.
                                </p>
                            </div>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- AVATAR UPLOAD FORM -->
    <form method="POST"
        action="{{ route('profile.avatar.update') }}"
        enctype="multipart/form-data"
        id="avatarForm"
        class="hidden">
        @csrf

        <input id="avatarInput"
            type="file"
            name="avatar"
            accept="image/png,image/jpeg,image/jpg,image/webp">
    </form>

    @include('components.email-verify-warning')

    <script>
        const avatarInput = document.getElementById('avatarInput');
        const avatarForm = document.getElementById('avatarForm');

        if (avatarInput && avatarForm) {
            avatarInput.addEventListener('change', async function () {
                if (!this.files || !this.files[0]) return;

                const file = this.files[0];

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {
                    alert('Chỉ cho phép ảnh JPG, PNG hoặc WEBP.');
                    this.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Ảnh không được vượt quá 2MB.');
                    this.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('avatar', file);

                try {
                    const response = await fetch("{{ route('profile.avatar.update') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        alert(data.message || 'File ảnh không hợp lệ.');
                        this.value = '';
                        return;
                    }

                    if (response.status === 419) {
                        alert('Phiên đăng nhập đã hết hạn. Hãy refresh trang rồi thử lại.');
                        this.value = '';
                        return;
                    }

                    if (!response.ok) {
                        const text = await response.text();
                        console.error(text);
                        alert('Upload lỗi HTTP ' + response.status + '.');
                        this.value = '';
                        return;
                    }

                    window.location.href = "{{ route('profile.edit') }}";
                } catch (error) {
                    console.error(error);
                    alert('Có lỗi xảy ra khi upload ảnh.');
                    this.value = '';
                }
            });
        }

        function setSidebarState(state) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const icon = document.getElementById('sidebarToggleIcon');

            const userInfo = document.getElementById('userInfo');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const texts = document.querySelectorAll('.sidebar-text');
            const avatar = document.getElementById('userAvatar');

            if (!sidebar || !mainContent || !icon || !userInfo || !toggleBtn || !avatar) return;

            const isCollapsed = state === 'collapsed';

            if (isCollapsed) {
                toggleBtn.style.left = 'calc(5rem - 18px)';

                userInfo.classList.add('justify-center', 'mx-auto', 'w-12', 'h-12');
                userInfo.classList.remove('gap-3');

                avatar.classList.remove('w-10', 'h-10');
                avatar.classList.add('w-8', 'h-8');
            } else {
                toggleBtn.style.left = 'calc(16rem - 18px)';

                userInfo.classList.remove('justify-center', 'mx-auto', 'w-12', 'h-12');
                userInfo.classList.add('gap-3');

                avatar.classList.remove('w-8', 'h-8');
                avatar.classList.add('w-10', 'h-10');
            }

            sidebar.classList.toggle('w-64', !isCollapsed);
            sidebar.classList.toggle('w-20', isCollapsed);

            mainContent.classList.toggle('ml-64', !isCollapsed);
            mainContent.classList.toggle('ml-20', isCollapsed);

            texts.forEach(el => {
                el.classList.toggle('hidden', isCollapsed);
            });

            icon.classList.toggle('fa-chevron-left', !isCollapsed);
            icon.classList.toggle('fa-chevron-right', isCollapsed);

            localStorage.setItem('sidebar', state);

            const menuItems = document.querySelectorAll('.sidebar-link');

            menuItems.forEach(item => {
                if (isCollapsed) {
                    item.classList.remove('gap-3', 'px-3');
                    item.classList.add('justify-center', 'px-0', 'mx-auto', 'w-12', 'h-12');
                } else {
                    item.classList.remove('justify-center', 'px-0', 'mx-auto', 'w-12', 'h-12');
                    item.classList.add('gap-3', 'px-3');
                }
            });
        }

        function toggleSidebar() {
            const currentState = localStorage.getItem('sidebar') || 'expanded';
            const nextState = currentState === 'expanded' ? 'collapsed' : 'expanded';

            setSidebarState(nextState);
        }

        setSidebarState(localStorage.getItem('sidebar') || 'expanded');
    </script>

    
</x-app-layout>