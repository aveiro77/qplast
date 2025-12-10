<section>
    <header class="mb-3">
        <h2 class="h5 mb-1">{{ __('Delete Account') }}</h2>
        <p class="small text-muted mb-0">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
    </header>

    <div class="mt-3">
        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="btn btn-danger">{{ __('Delete Account') }}</x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-3" x-data="{ confirm: false }">
            @csrf
            @method('delete')

            <div class="alert alert-danger d-flex align-items-start">
                <div class="me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.98 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.71c.889 0 1.438-.99.98-1.767L8.98 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1-2.002 0 1 1 0 0 1 2.002 0z"/>
                    </svg>
                </div>
                <div>
                    <h6 class="mb-1">{{ __('Permanently delete account') }}</h6>
                    <p class="small mb-0 text-muted">{{ __('Deleting your account will remove all data permanently. This action cannot be undone.') }}</p>
                </div>
            </div>

            <div class="mt-3 mb-2">
                <x-input-label for="password" value="{{ __('Password') }}" class="form-label" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control w-75"
                    placeholder="{{ __('Password') }}"
                    required
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="text-danger small mt-1" />
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="confirm_delete" x-model="confirm">
                <label class="form-check-label small text-muted" for="confirm_delete">
                    {{ __('I understand this action is permanent and I want to delete my account.') }}
                </label>
            </div>

            <div class="d-flex justify-content-end">
                <x-secondary-button x-on:click="$dispatch('close')" class="btn btn-outline-secondary">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="btn btn-danger ms-2" x-bind:disabled="!confirm">
                    <i class="bi bi-trash me-1"></i> {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
