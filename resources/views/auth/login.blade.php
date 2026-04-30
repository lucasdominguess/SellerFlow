<x-guest-layout>
    @push('styles')
        @vite('resources/css/pages/login.css')
    @endpush

    <div class="glass-panel bg-surface-container-low/40 rounded-[2.5rem] p-8 md:p-12 shadow-[0_24px_48px_-12px_rgba(0,0,0,0.5)] flex flex-col items-center">
        <!-- Brand Lockup -->
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-on-surface mb-2 font-sans">Welcome Back</h1>
            <p class="text-on-surface-variant font-label text-sm tracking-wide">Enter the ethereal space to continue</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="w-full space-y-8" id="login-form">
            @csrf

            <!-- Email Field -->
            <div class="relative group">
                <label class="block text-xs font-label uppercase tracking-widest text-outline mb-1 px-1" for="email">
                    Email Address
                </label>
                <input class="w-full bg-transparent border-0 border-b border-outline-variant/30 py-3 px-1 text-on-surface placeholder:text-outline-variant/50 focus:ring-0 focus:border-primary transition-all duration-300 outline-none" 
                       id="email" 
                       name="email"
                       value="{{ old('email') }}"
                       required 
                       autofocus
                       autocomplete="username"
                       placeholder="name@domain.com" 
                       type="email"/>
                <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-primary transition-all duration-500 group-focus-within:w-full"></div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-error" />
            </div>

            <!-- Password Field -->
            <div class="relative group">
                <div class="flex justify-between items-end mb-1 px-1">
                    <label class="block text-xs font-label uppercase tracking-widest text-outline" for="password">Parameter</label>
                    @if (Route::has('password.request'))
                        <a class="text-[10px] font-label uppercase tracking-widest text-primary/70 hover:text-primary transition-colors" href="{{ route('password.request') }}">
                            Forgot?
                        </a>
                    @endif
                </div>
                <input class="w-full bg-transparent border-0 border-b border-outline-variant/30 py-3 px-1 text-on-surface placeholder:text-outline-variant/50 focus:ring-0 focus:border-primary transition-all duration-300 outline-none" 
                       id="password" 
                       type="password"
                       name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••" />
                <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-primary transition-all duration-500 group-focus-within:w-full"></div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-error" />
            </div>

            <!-- Remeber Me -->
            <div class="block mt-4 px-1">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                    <span class="ml-2 text-sm text-on-surface-variant">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- CTA -->
            <div class="pt-4">
                <button class="w-full bg-gradient-to-br from-primary to-primary-container text-on-primary font-bold py-4 rounded-full shadow-[0_8px_20px_rgba(85,22,190,0.3)] hover:shadow-[0_12px_28px_rgba(85,22,190,0.4)] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2" type="submit">
                    <span>Login</span>
                    <span class="material-symbols-outlined text-sm">login</span>
                </button>
            </div>
        </form>

        <!-- Social/Alternative -->
        <div class="w-full mt-10">
            <div class="relative flex items-center mb-8">
                <div class="flex-grow border-t border-outline-variant/10"></div>
                <span class="flex-shrink mx-4 text-[10px] font-label uppercase tracking-[0.2em] text-outline-variant">Or verify via</span>
                <div class="flex-grow border-t border-outline-variant/10"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" class="flex items-center justify-center gap-3 bg-surface-container-highest/30 border border-outline-variant/10 hover:bg-surface-container-highest/50 py-3 rounded-xl transition-all active:scale-95 group">
                    <span class="material-symbols-outlined text-violet-300" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
                    <span class="text-xs font-label tracking-widest uppercase text-on-surface-variant group-hover:text-on-surface">Biometrics</span>
                </button>
                <button type="button" class="flex items-center justify-center gap-3 bg-surface-container-highest/30 border border-outline-variant/10 hover:bg-surface-container-highest/50 py-3 rounded-xl transition-all active:scale-95 group">
                    <span class="material-symbols-outlined text-violet-300">key</span>
                    <span class="text-xs font-label tracking-widest uppercase text-on-surface-variant group-hover:text-on-surface">Passkey</span>
                </button>
            </div>
        </div>

        <p class="mt-12 text-xs font-label text-outline-variant text-center">
            New to the core? 
            @if (Route::has('register'))
                <a class="text-primary font-bold hover:underline underline-offset-4 ml-1" href="{{ route('register') }}">Request access</a>
            @else
                <span class="text-primary font-bold ml-1 cursor-not-allowed">Request access</span>
            @endif
        </p>
    </div>

    @push('scripts')
        @vite('resources/js/pages/login.js')
    @endpush
</x-guest-layout>
