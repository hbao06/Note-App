@if (!auth()->user()->hasVerifiedEmail())
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-2xl">
        <div class="flex justify-center items-center px-5 py-4 rounded-2xl 
            bg-yellow-50 border border-yellow-200 text-yellow-900 shadow-xl">
            
            <span class="text-center text-base">
                Tài khoản của bạn chưa được xác minh. Vui lòng kiểm tra email để hoàn tất.
            </span>
        </div>
    </div>
@endif