<header>
    <h5 class="card-title">Thông Tin Hồ Sơ</h5>
    <p class="text-muted small mt-1">
        Cập nhật thông tin hồ sơ và địa chỉ email của tài khoản của bạn.
    </p>
</header>

<form method="post" action="{{ route('profile.update') }}" class="mt-4">
    @csrf
    @method('patch')

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                <x-input-error class="mt-2 text-danger small" :messages="$errors->get('name')" />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                <x-input-error class="mt-2 text-danger small" :messages="$errors->get('email')" />
            </div>

            <div class="d-flex align-items-center gap-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                @if (session('status') === 'profile-updated')
                    <p class="text-success small m-0">{{ __('Saved.') }}</p>
                @endif
            </div>
        </div>
    </div>
</form>