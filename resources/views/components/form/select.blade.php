@props([
    'label'=>null,
    'name',
    'selected'=>null,
    'required'=>false,
    'placeholder' => '-- ' . __('site.select') . ' --',
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'select2' => false
])

@if($label)
    <x-form.label
            :for="$name"
            :required="$required">
        {{ $label }}
    </x-form.label>
@endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'form-select form-control'
                        . ($errors->has($name) ? ' is-invalid' : '')
                        . ($select2 ? ' select2' : '')
        ]) }}>

    @if (!$required)
        <option value="">
            {{ $placeholder }}
        </option>
    @endif

    @foreach($options as $key => $option)
        @php
            $isKeyValue = is_scalar($option);

            $value = $isKeyValue
                ? $key
                : data_get($option, $optionValue);

            $label = $isKeyValue
                ? $option
                : data_get($option, $optionLabel);
        @endphp

        <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
            {{ $label }}
        </option>
    @endforeach
</select>

<x-form.error :name="$name"/>