<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - My Notes</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#f5f3ef] text-slate-950">
    <div class="min-h-screen relative overflow-hidden flex items-center justify-center px-6 py-10">

        <!-- Background -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[900px] rounded-full bg-white blur-3xl opacity-80"></div>
            <div class="absolute top-20 -right-32 w-[520px] h-[520px] rounded-full bg-orange-100 blur-3xl opacity-70"></div>
            <div class="absolute bottom-0 -left-32 w-[520px] h-[520px] rounded-full bg-slate-200 blur-3xl opacity-70"></div>
        </div>

        <div class="relative z-10 w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">

            <!-- LEFT -->
            <div class="hidden lg:block">
                <a href="/" class="inline-flex items-center gap-3 mb-12">
                    <div class="w-11 h-11 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-black shadow-lg shadow-slate-300/40">
                        N
                    </div>
                    <div>
                        <div class="text-xl font-black leading-none">My Notes</div>
                        <div class="text-xs text-slate-400 mt-1">Minimal note workspace</div>
                    </div>
                </a>

                <h1 class="text-6xl font-black tracking-tight leading-tight">
                    Tiếp tục với
                    <span class="block text-slate-500 mt-2">My Notes.</span>
                </h1>

                <p class="mt-7 text-lg text-slate-500 leading-8 max-w-xl">
                    Quản lý ghi chú, ý tưởng và nội dung cá nhân trong một workspace gọn gàng, bảo mật và dễ tập trung.
                </p>

                <div class="mt-10 grid grid-cols-3 gap-4 max-w-xl">
                    <div class="bg-white/80 border border-white rounded-[1.6rem] p-5 shadow-sm">
                        <div class="text-2xl font-black">Fast</div>
                        <p class="mt-1 text-sm text-slate-500">Truy cập nhanh</p>
                    </div>

                    <div class="bg-white/80 border border-white rounded-[1.6rem] p-5 shadow-sm">
                        <div class="text-2xl font-black">Secure</div>
                        <p class="mt-1 text-sm text-slate-500">Bảo mật tốt</p>
                    </div>

                    <div class="bg-white/80 border border-white rounded-[1.6rem] p-5 shadow-sm">
                        <div class="text-2xl font-black">Clean</div>
                        <p class="mt-1 text-sm text-slate-500">Gọn gàng</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="w-full max-w-md mx-auto">
                <div class="mb-8 text-center lg:hidden">
                    <a href="/" class="inline-flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-black">
                            N
                        </div>
                        <span class="text-xl font-black">My Notes</span>
                    </a>
                </div>

                <div class="bg-white/90 backdrop-blur-xl border border-white rounded-[2rem] shadow-2xl shadow-slate-300/40 p-8">
                    <div class="mb-8">
                        <h2 class="text-3xl font-black tracking-tight">Log in</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Enter your account details to access your workspace.
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-sm font-bold text-slate-700" />
                            <x-text-input id="email"
                                class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 px-4 py-3.5 text-slate-900 placeholder:text-slate-400 focus:border-slate-950 focus:ring-slate-950"
                                type="email"
                                name="email"
                                :value="old('email')"
                                placeholder="you@example.com"
                                required
                                autofocus
                                autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-sm font-bold text-slate-700" />
                            <x-text-input id="password"
                                class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 px-4 py-3.5 text-slate-900 placeholder:text-slate-400 focus:border-slate-950 focus:ring-slate-950"
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-500">
                                <input id="remember_me"
                                    type="checkbox"
                                    class="rounded border-slate-300 text-slate-950 shadow-sm focus:ring-slate-950"
                                    name="remember">
                                <span>Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm font-bold text-slate-950 hover:underline"
                                   href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full py-4 rounded-2xl bg-slate-950 text-white font-black shadow-xl shadow-slate-300/50 hover:bg-slate-800 active:scale-[0.98] transition">
                            Log in
                        </button>

                        <p class="text-center text-sm text-slate-500">
                            Don’t have an account?
                            <a href="{{ route('register') }}" class="font-bold text-slate-950 hover:underline">
                                Create account
                            </a>
                        </p>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-slate-400">
                    Simple. Secure. Modern notes workspace.
                </p>
            </div>

        </div>
    </div>
</body>
</html>