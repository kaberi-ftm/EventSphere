<x-guest-layout>

    <div class="auth-card">

        <div class="auth-heading">
            <h2>Create an account</h2>

            <p>
                Register to participate in EventSphere events.
            </p>
        </div>

        <form method="POST"
              action="{{ route('register') }}">

            @csrf

            <div class="auth-field">
                <label for="name"
                       class="auth-label">
                    Full name
                </label>

                <input id="name"
                       type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="auth-input"
                       autocomplete="name"
                       autofocus
                       required>

                @error('name')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

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
                       autocomplete="new-password"
                       required>

                @error('password')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password_confirmation"
                       class="auth-label">
                    Confirm password
                </label>

                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       class="auth-input"
                       autocomplete="new-password"
                       required>
            </div>

            <button type="submit"
                    class="auth-button">
                Create account
            </button>

        </form>

        <div class="auth-switch">
            Already registered?

            <a href="{{ route('login') }}"
               class="auth-link">
                Log in
            </a>
        </div>

    </div>

</x-guest-layout>