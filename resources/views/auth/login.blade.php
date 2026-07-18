<x-guest-layout>

    <div class="auth-card">

        <div class="auth-heading">
            <h2>Welcome back</h2>

            <p>
                Log in to access your EventSphere dashboard.
            </p>
        </div>

        @if(session('status'))
            <div class="auth-session">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('login') }}">

            @csrf

            <div class="auth-field">
                <label for="email"
                       class="auth-label">
                    Email address
                </label>

                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="auth-input"
                       autocomplete="username"
                       autofocus
                       required>

                @error('email')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password"
                       class="auth-label">
                    Password
                </label>

                <input id="password"
                       type="password"
                       name="password"
                       class="auth-input"
                       autocomplete="current-password"
                       required>

                @error('password')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="auth-options">

                <label class="auth-remember">
                    <input type="checkbox"
                           name="remember">

                    <span>Remember me</span>
                </label>

                @if(\Illuminate\Support\Facades\Route::has(
                    'password.request'
                ))
                    <a href="{{ route(
                        'password.request'
                    ) }}"
                       class="auth-link">
                        Forgot password?
                    </a>
                @endif

            </div>

            <button type="submit"
                    class="auth-button">
                Log in
            </button>

        </form>

        @if(\Illuminate\Support\Facades\Route::has(
            'register'
        ))
            <div class="auth-switch">
                Do not have an account?

                <a href="{{ route('register') }}"
                   class="auth-link">
                    Create account
                </a>
            </div>
        @endif

    </div>

</x-guest-layout>