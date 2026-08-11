@props([
    'label' => null,
    'name',
    'value' => null,
    'required' => false,
    'placeholder' => 'dd/mm/yyyy',
    'format' => 'DD/MM/YYYY',
    'minDate' => null,
    'maxDate' => null,
])

@if($label)
    <x-form.label
            :for="$name"
            :required="$required">
        {{ $label }}
    </x-form.label>
@endif

<input
        type="text"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        data-format="{{ $format }}"
        @if($minDate)
            data-min-date="{{ $minDate }}"
        @endif
        @if($maxDate)
            data-max-date="{{ $maxDate }}"
        @endif

        {{
            $attributes->class([
                'form-control',
                'datepicker',
                'is-invalid' => $errors->has($name),
            ])
        }}
>

<x-form.error :name="$name" />

@once
    @push('scripts')
        <script>
            $(function () {

                $('.datepicker').each(function () {

                    let format = $(this).data('format') || 'DD/MM/YYYY';

                    let options = {
                        singleDatePicker: true,
                        showDropdowns: true,
                        autoUpdateInput: false,
                        locale: {
                            format: format
                        }
                    };

                    if ($(this).data('min-date')) {
                        options.minDate = $(this).data('min-date');
                    }

                    if ($(this).data('max-date')) {
                        options.maxDate = $(this).data('max-date');
                    }

                    $(this).daterangepicker(options);

                    if ($(this).val()) {
                        let picker = $(this).data('daterangepicker');
                        picker.setStartDate($(this).val());
                        picker.setEndDate($(this).val());
                    }

                    $(this).on('apply.daterangepicker', function (ev, picker) {
                        $(this).val(
                            picker.startDate.format(format)
                        );
                    });

                    $(this).on('cancel.daterangepicker', function () {
                        $(this).val('');
                    });

                });

            });
        </script>
    @endpush
@endonce