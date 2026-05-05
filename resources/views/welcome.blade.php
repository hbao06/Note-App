<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Notes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#f5f3ef] text-slate-950">
    <div class="min-h-screen relative overflow-hidden">

        <!-- Background -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[900px] rounded-full bg-white blur-3xl opacity-80"></div>
            <div class="absolute top-32 -right-32 w-[520px] h-[520px] rounded-full bg-orange-100 blur-3xl opacity-70"></div>
            <div class="absolute bottom-0 -left-36 w-[520px] h-[520px] rounded-full bg-slate-200 blur-3xl opacity-70"></div>
        </div>

        <!-- Nav -->
        <header class="relative z-20 px-5 pt-5">
            <nav class="max-w-7xl mx-auto h-16 px-4 sm:px-6 rounded-[1.6rem] bg-white/75 backdrop-blur-xl border border-white/80 shadow-sm flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-black">
                        N
                    </div>
                    <div>
                        <div class="font-black leading-none">My Notes</div>
                        <div class="text-xs text-slate-400 mt-1">Minimal note workspace</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('notes.index') }}"
                           class="px-5 py-2.5 rounded-2xl bg-slate-950 text-white text-sm font-bold hover:bg-slate-800 active:scale-95 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="hidden sm:inline-flex px-4 py-2.5 rounded-2xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                            Log in
                        </a>

                        <a href="{{ route('register') }}"
                           class="px-5 py-2.5 rounded-2xl bg-slate-950 text-white text-sm font-bold shadow-lg shadow-slate-300/40 hover:bg-slate-800 active:scale-95 transition">
                            Register
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="relative z-10">
            <!-- Hero -->
            <section class="max-w-7xl mx-auto px-5 pt-16 pb-20 grid lg:grid-cols-2 gap-14 items-center">

                <!-- Left -->
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 border border-white shadow-sm text-sm font-medium text-slate-600 mb-7">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Private notes · Labels · Sharing · Lock
                    </div>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black tracking-[-0.055em] leading-[0.95]">
                        Notes that feel
                        <span class="block text-slate-500">clean & effortless.</span>
                    </h1>

                    <p class="mt-7 max-w-xl text-lg leading-8 text-slate-500">
                        Không gian ghi chú hiện đại giúp bạn viết nhanh, tìm nhanh,
                        phân loại rõ ràng và bảo mật nội dung quan trọng.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('notes.index') }}"
                               class="px-7 py-4 rounded-2xl bg-slate-950 text-white font-bold shadow-xl shadow-slate-300/50 hover:bg-slate-800 active:scale-95 transition">
                                Open workspace
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="px-7 py-4 rounded-2xl bg-slate-950 text-white font-bold shadow-xl shadow-slate-300/50 hover:bg-slate-800 active:scale-95 transition">
                                Start for free
                            </a>

                            <a href="{{ route('login') }}"
                               class="px-7 py-4 rounded-2xl bg-white/80 border border-white text-slate-700 font-bold hover:bg-white transition">
                                Log in
                            </a>
                        @endauth
                    </div>

                    <div class="mt-12 grid grid-cols-3 gap-3 max-w-xl">
                        <div class="rounded-[1.5rem] bg-white/75 border border-white p-5 shadow-sm">
                            <div class="text-2xl font-black">2x</div>
                            <p class="mt-1 text-sm text-slate-500">Faster writing</p>
                        </div>

                        <div class="rounded-[1.5rem] bg-white/75 border border-white p-5 shadow-sm">
                            <div class="text-2xl font-black">100%</div>
                            <p class="mt-1 text-sm text-slate-500">Private notes</p>
                        </div>

                        <div class="rounded-[1.5rem] bg-white/75 border border-white p-5 shadow-sm">
                            <div class="text-2xl font-black">∞</div>
                            <p class="mt-1 text-sm text-slate-500">Organized ideas</p>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="relative">
                    <div class="absolute -inset-4 rounded-[3rem] bg-gradient-to-tr from-slate-300/50 via-white to-orange-100/70 blur-2xl"></div>

                    <div class="relative rounded-[2.4rem] bg-slate-950 p-3 shadow-2xl shadow-slate-400/40">
                        <div class="rounded-[1.9rem] overflow-hidden bg-white">

                            <div class="h-14 px-5 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                                </div>
                                <div class="text-xs font-semibold text-slate-400">my-notes.app</div>
                            </div>

                            <div class="grid grid-cols-[76px_1fr] min-h-[500px]">
                                <aside class="bg-slate-50 border-r border-slate-100 p-4 flex flex-col items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-black">
                                        T
                                    </div>

                                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-900">
                                        <i class="fa-solid fa-note-sticky"></i>
                                    </div>

                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-user-group"></i>
                                    </div>

                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-gear"></i>
                                    </div>

                                    <div class="mt-auto w-12 h-12 rounded-2xl flex items-center justify-center text-red-400">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </div>
                                </aside>

                                <div class="p-6 sm:p-7">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-slate-400">Good morning ✨</p>
                                            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Your Notes</h2>
                                        </div>

                                        <button class="px-5 py-3 rounded-2xl bg-slate-950 text-white font-bold shadow-lg shadow-slate-300/50">
                                            + Create
                                        </button>
                                    </div>

                                    <div class="mt-6 h-14 rounded-2xl border border-slate-200 bg-slate-50/70 flex items-center px-5 text-slate-400">
                                        <i class="fa-solid fa-magnifying-glass mr-3"></i>
                                        Search notes...
                                    </div>

                                    <div class="mt-5 flex flex-wrap gap-2">
                                        <span class="px-4 py-2 rounded-full bg-slate-950 text-white text-sm font-bold">All</span>
                                        <span class="px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-500 text-sm font-medium">Private</span>
                                        <span class="px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-500 text-sm font-medium">Shared</span>
                                    </div>

                                    <div class="mt-7 grid sm:grid-cols-2 gap-4">
                                        <div class="rounded-[1.6rem] bg-[#fff8e8] border border-orange-100 p-5 min-h-[220px] shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <span class="px-3 py-1 rounded-full bg-white/70 text-xs font-bold text-slate-600">Pinned</span>
                                                <i class="fa-solid fa-thumbtack rotate-45 text-orange-400"></i>
                                            </div>

                                            <h3 class="mt-5 text-2xl font-black">Project Planning</h3>
                                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                                Outline key ideas, track progress, and keep everything organized in one place.
                                            </p>

                                            <div class="mt-6 text-xs font-medium text-slate-400">
                                                <i class="fa-regular fa-clock"></i> Updated 3 days ago
                                            </div>
                                        </div>

                                        <div class="rounded-[1.6rem] bg-slate-950 text-white p-5 min-h-[220px] shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold">Locked</span>
                                                <i class="fa-solid fa-lock text-yellow-300"></i>
                                            </div>

                                            <h3 class="mt-5 text-2xl font-black">Confidential Notes</h3>
                                            <p class="mt-3 text-sm leading-6 text-slate-300">
                                                Secure sensitive information with password protection and restricted access.
                                            </p>

                                            <div class="mt-6 inline-flex px-3 py-1 rounded-full bg-white/10 text-xs text-slate-200">
                                                Private
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="max-w-7xl mx-auto px-5 pb-20">
                <div class="grid md:grid-cols-3 gap-5">
                    <div class="rounded-[2rem] bg-white/75 border border-white p-7 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-bolt bg-yellow-100 text-yellow-600"></i>
                            </div>

                            <h3 class="text-lg font-black">Fast workflow</h3>
                        </div>
                        <p class="mt-3 text-slate-500 leading-7">
                            Viết note, chỉnh sửa và tìm kiếm nhanh trong một workspace gọn gàng.
                        </p>
                    </div>

                    <div class="rounded-[2rem] bg-white/75 border border-white p-7 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-shield-halved bg-green-100 text-green-600"></i>
                            </div>

                            <h3 class="text-lg font-black">Private & secure</h3>
                        </div>
                        <p class="mt-3 text-slate-500 leading-7">
                            Khóa ghi chú quan trọng và giữ nội dung cá nhân an toàn hơn.
                        </p>
                    </div>

                    <div class="rounded-[2rem] bg-white/75 border border-white p-7 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-user-group bg-blue-100 text-blue-600"></i>
                            </div>

                            <h3 class="text-lg font-black">Easy sharing</h3>
                        </div>
                        <p class="mt-3 text-slate-500 leading-7">
                            Chia sẻ note cho người khác với trải nghiệm rõ ràng, dễ dùng.
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>