<div class="btn-group mt-2" style="width: 112px">
    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ __('common.account.title') }} <i class="mdi mdi-chevron-down"></i>
    </button>
    <div class="dropdown-menu">
        @if ( !$employee->user?->activated_at && $employee->user?->status === \App\Enums\UserStatus::INACTIVE )
            <x-form.confirm-button
                    :action="route('employees.resend-activation', $employee)"
                    :title="__('common.messages.confirm_resend_activation')"
                    :text="__('common.messages.resend_activation_description')"
                    :confirm-text="__('common.button.resend')"
                    :cancel-text="__('common.button.cancel')"
                    icon="fas fa-envelope"
                    class="dropdown-item"
                    :label="__('common.account.resend_activation')"
            />
        @endif
        @if ( $employee->user?->status === \App\Enums\UserStatus::ACTIVE )
            <x-form.confirm-button
                    :action="route('employees.reset-password',['employee' => $employee])"
                    :title="__('common.messages.confirm_reset_password')"
                    :text="__('common.messages.reset_password_description')"
                    :confirm-text="__('common.button.reset_password')"
                    :cancel-text="__('common.button.cancel')"
                    :label="__('common.button.reset_password')"
                    icon="fas fa-key"
                    class="dropdown-item"
            />
        @endif
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">Separated link</a>
    </div>
</div><!-- /btn-group -->