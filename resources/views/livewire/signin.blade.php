<div class="flex justify-center items-center h-screen bg-gradient-to-tr from-green-500 to-green-200 font-noto_thai">
    <div class="grid grid-cols-2 h-4/6 w-1/2">
        <div
            class="w-full bg-gradient-to-t from-indigo-500 to-blue-500 pl-3 shadow-lg rounded-tl-xl rounded-bl-xl flex flex-col justify-center text-center gap-5">
            <h1 class="text-white text-2xl text-pretty font-serif">Manage your own shop with convenience system</h1>
            <i class="fa-solid fa-shop text-9xl text-white"></i>
        </div>

        <div class="w-full bg-yellow-400 p-6 shadow-md rounded-tr-xl rounded-br-xl flex flex-col justify-center">
            {{-- <img src="https://i.pinimg.com/736x/57/b0/f5/57b0f501e7f65635b4add0fca9583988.jpg" alt="logo" class="block mx-auto h-auto w-1/2"> --}}
            <form wire:submit="signin">
                <label for="username">Username</label>
                <input type="text" wire:model="username" class="form-control">
                @if (isset($errorUsername))
                    <div class="text-red-500 mt-2">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ $errorUsername }}
                    </div>
                @endif

                <label for="password" class="mt-4">Password</label>
                <input type="password" wire:model="password" class="form-control">
                @if (isset($errorPassword))
                    <div class="text-red-500 mt-2">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ $errorPassword }}
                    </div>
                @endif
                @if (session('addError'))
                    <div class="text-red-500 mt-2">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ session('addError') }}
                    </div>
                @endif

                <button type="submit" class="btn-primary mt-5 font-bold drop-shadow-md w-full">Sign In</button>
            </form>

            @if (isset($error))
                <div class="text-red-500 mt-4">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    {{ $error }}
                </div>
            @endif
        </div>
    </div>
</div>

