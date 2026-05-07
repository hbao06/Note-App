<div id="mainContent" class="relative h-screen overflow-hidden transition-all duration-300 ml-64 bg-gray-50">

    <div class="h-full overflow-y-auto bg-gray-50 text-gray-900 pb-28">
        <div class="max-w-4xl mx-auto px-6 py-10 space-y-6">

            <!-- HERO PROFILE -->
            <div class="relative overflow-hidden rounded-[2rem] bg-white border border-gray-200 shadow-sm">
                <div class="h-32 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-600"></div>

                <div class="px-8 pb-8">
                    <div class="-mt-12 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
                        <div class="flex items-end gap-5">
                            <div class="w-28 h-28 rounded-full bg-gray-950 text-white flex items-center justify-center text-5xl font-bold ring-4 ring-white shadow-lg">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>

                            <div class="pb-2">
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