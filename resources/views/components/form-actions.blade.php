<button type="submit"
        class="btn btn-primary waves-effect waves-light btn-sm px-4 mt-3 float-right mb-0 ml-2">
    <i class="fas fa-save"></i> {{ __('common.button.save') }}
</button>

@if($showReset ?? true)
    <button type="reset"
            class="btn btn-outline-danger waves-effect waves-light btn-sm px-4 mt-3 float-right mb-0 ml-2">
        <i class="fas fa-undo"></i> {{ __('common.button.reset') }}
    </button>
@endif

@if(($showCancel ?? true) && isset($urlCancel))
    <button type="button"
            class="btn btn-outline-warning waves-effect waves-light btn-sm px-4 mt-3 float-left"
            onclick="window.location.href='{{ $urlCancel }}'">
        <i class="fas fa-arrow-left"></i>
        {{ __('common.button.cancel') }}
    </button>
@endif
