<div class="min-h-screen bg-blue-100 font-noto_thai p-4 flex items-center">
    <div class="max-w-6xl mx-auto h-full flex items-center justify-center">
        <!-- Main container - make it responsive -->
        <div class="w-full md:w-4/5 lg:w-3/4 xl:w-1/2 grid grid-cols-1 md:grid-cols-2 shadow-xl rounded-xl overflow-hidden">
            
            <!-- Left side - Shop management section -->
            <div class="w-full bg-gradient-to-t from-indigo-500 to-blue-500 p-6 flex flex-col justify-center items-center text-center gap-5">
                <h1 class="text-white text-xl md:text-2xl lg:text-3xl font-serif">
                    Manage your own shop with convenience system
                </h1>
                <i class="fa-solid fa-shop text-6xl md:text-7xl lg:text-9xl text-white"></i>
            </div>
            
            <!-- Right side - Login form -->
            <div class="w-full bg-yellow-400 p-4 md:p-6 flex flex-col justify-center">
                <form wire:submit="signin" class="space-y-4">
                    <div class="space-y-2">
                        <label for="username" class="block text-sm md:text-base font-medium">Username</label>
                        <input 
                            type="text" 
                            wire:model="username" 
                            class="w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @if (isset($errorUsername))
                            <div class="text-red-500 text-sm">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                {{ $errorUsername }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-sm md:text-base font-medium">Password</label>
                        <input 
                            type="password" 
                            wire:model="password" 
                            class="w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @if (isset($errorPassword))
                            <div class="text-red-500 text-sm">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                {{ $errorPassword }}
                            </div>
                        @endif
                    </div>

                    @if (session('addError'))
                        <div class="text-red-500 text-sm">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            {{ session('addError') }}
                        </div>
                    @endif

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 ease-in-out"
                    >
                        Sign In
                    </button>
                </form>

                @if (isset($error))
                    <div class="text-red-500 mt-4 text-sm">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ $error }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>