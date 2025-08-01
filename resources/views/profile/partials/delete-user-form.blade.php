<header>
    <h5 class="card-title text-danger">{{ __('Delete Account') }}</h5>
    <p class="text-muted small mt-1">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
    </p>
</header>

<div class="mt-3">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        {{ __('Delete Account') }}
    </button>
</div>

<!-- Modal của Bootstrap -->
<div class="modal fade" id="confirm-user-deletion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
            @csrf
            @method('delete')
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Are you sure you want to delete your account?') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
                <div class="mt-3">
                    <label for="password_delete_modal" class="form-label sr-only">{{ __('Password') }}</label>
                    <input id="password_delete_modal" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}">
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-danger small" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
            </div>
        </form>
    </div>
</div>