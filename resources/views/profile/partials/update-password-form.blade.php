<header>
    <h5 class="card-title">{{ __('Update Password') }}</h5>
    <p class="text-muted small mt-1">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>
</header>

<form method="post" action="{{ route('password.update') }}" class="mt-4">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-danger small" />
            </div>

            <div class="mb-3">
                <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
                <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger small" />
            </div>

            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger small" />
            </div>

            <div class="d-flex align-items-center gap-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                @if (session('status') === 'password-updated')
                    <p class="text-success small m-0">{{ __('Saved.') }}</p>
                @endif
            </div>
        </div>
    </div>
</form>