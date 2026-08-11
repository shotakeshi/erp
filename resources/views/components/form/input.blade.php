{{--
|--------------------------------------------------------------------------
| Form Input Component
|--------------------------------------------------------------------------
|
| Usage:
|
| Basic
| <x-form.input
|     name="first_name"
|     label="First Name"
| />
|
| Required
| <x-form.input
|     name="email"
|     label="Email"
|     required
| />
|
| With Value
| <x-form.input
|     name="email"
|     :value="$employee->email"
| />
|
| Placeholder
| <x-form.input
|     name="phone"
|     placeholder="090xxxxxxx"
| />
|
| Currency
| <x-form.input
|     name="salary"
|     format="currency"
| />
|
| Number
| <x-form.input
|     name="quantity"
|     format="number"
| />
|
| Uppercase
| <x-form.input
|     name="employee_id"
|     format="uppercase"
| />
|
| Lowercase
| <x-form.input
|     name="email"
|     format="lowercase"
| />
|
| Readonly
| <x-form.input
|     name="code"
|     readonly
| />
|
| Disabled
| <x-form.input
|     name="status"
|     disabled
| />
|
| Description
| <x-form.input
|     name="password"
|     description="Password must be at least 8 characters."
| />
|
|--------------------------------------------------------------------------
| Available Props
|--------------------------------------------------------------------------
|
| name          string      Required
| label         string|null
| type          text|email|password|number...
| value         mixed
| placeholder   string
| required      bool
| readonly      bool
| disabled      bool
| autofocus     bool
| format        currency|number|uppercase|lowercase
| description   string|null
|
--}}

@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'autofocus' => false,
    'format' => null,
    'help' => null,
])

@if($label)
    <x-form.label
            :for="$name"
            :required="$required">
        {{ $label }}
    </x-form.label>
@endif

<input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"

        @readonly($readonly)
        @disabled($disabled)
        @autofocus($autofocus)

        @if($format)
            data-format="{{ $format }}"
        @endif

        {{
            $attributes->class([
                'form-control',
                'is-invalid' => $errors->has($name),
            ])
        }}
>

@if($help)
    <small class="text-muted">
        {{ $help }}
    </small>
@endif

<x-form.error :name="$name"/>