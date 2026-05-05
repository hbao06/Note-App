<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - My Notes</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#f5f3ef] text-slate-950">
    <div class="min-h-screen relative overflow-hidden flex items-center justify-center px-5 py-10">

        <!-- Background -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[850px] h-[850px] rounded-full bg-white blur-3xl opacity-80"></div>
            <div class="absolute top-20 -right-32 w-[500px] h-[500px] rounded-full bg-orange-100 blur-3xl opacity-70"></div>
            <div class="absolute bottom-0 -left-32 w-[500px] h-[500px] rounded-full bg-slate-200 blur-3xl opacity-70"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">

            <div class="rounded-[2rem] bg-white/85 backdrop-blur-xl border border-white shadow-2xl shadow-slate-300/40 p-7 sm:p-8">

                <!-- Logo -->
                <div class="mb-8">
                    <a href="/" class="inline-flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-black shadow-lg shadow-slate-300/40">
                            N
                        </div>
                        <div>
                            <div class="text-xl font-black leading-none">My Notes</div>
                            <div class="text-xs text-slate-400 mt-1">Minimal note workspace</div>
                        </div>
                    </a>
                </div>

                <div class="mb-7">
                    <h1 class="text-3xl font-black tracking-tight">
                        Forgot password?
                    </h1>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Enter your email and we’ll send you a reset link to create a new password.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status
                    class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700"
                    :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-sm font-bold text-slate-700" />

                        <x-text-input id="email"
                            class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 px-4 py-3.5 text-slate-900 placeholder:text-slate-400 focus:border-slate-950 focus:ring-slate-950"
                            type="email"
                            name="email"
                            :value="old('email')"
                            placeholder="you@example.com"
                            required
                            autofocus />

                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Button -->
                    <button type="submit"
                        class="w-full py-4 rounded-2xl bg-slate-950 text-white font-black shadow-xl shadow-slate-300/50 hover:bg-slate-800 active:scale-[0.98] transition">
                        Send reset link
                    </button>

                    <!-- Back to login -->
                    <p class="text-center text-sm text-slate-500">
                        Remember your password?
                        <a href="{{ route('login') }}" class="font-bold text-slate-950 hover:underline">
                            Log in
                        </a>
                    </p>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                Secure password recovery for your notes workspace.
            </p>
        </div>
    </div>
</body>
</html>