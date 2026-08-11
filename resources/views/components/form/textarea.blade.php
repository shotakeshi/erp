{{--
|--------------------------------------------------------------------------
| Form Textarea Component
|--------------------------------------------------------------------------
|
| Usage:
|
| Basic
| <x-form.textarea
|     name="description"
|     label="Description"
| />
|
| Required
| <x-form.textarea
|     name="description"
|     label="Description"
|     required
| />
|
| With Value
| <x-form.textarea
|     name="description"
|     :value="$employee->description"
| />
|
| Placeholder
| <x-form.textarea
|     name="description"
|     placeholder="Enter description..."
| />
|
| Rows
| <x-form.textarea
|     name="description"
|     :rows="5"
| />
|
| Readonly
| <x-form.textarea
|     name="description"
|     readonly
| />
|
| Disabled
| <x-form.textarea
|     name="description"
|     disabled
| />
|
| Description
| <x-form.textarea
|     name="description"
|     description="Maximum 500 characters."
| />
|
|--------------------------------------------------------------------------
| Available Props
|--------------------------------------------------------------------------
|
| name          string      Required
| label         string|null
| value         mixed
| placeholder   string
| rows          int (default: 3)
| required      bool
| readonly      bool
| disabled      bool
| autofocus     bool
| description   string|null
|
--}}

@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'rows' => 3,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'autofocus' => false,
    'description' => null,
])

@if($label)
    <x-form.label
            :for="$name"
            :required="$required">
        {{ $label }}
    </x-form.label>
@endif

<textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"

        @required($required)
        @readonly($readonly)
        @disabled($disabled)
        @autofocus($autofocus)

    {{
        $attributes->class([
            'form-control',
            'is-invalid' => $errors->has($name),
        ])
    }}
>{{ old($name, $value) }}</textarea>

@if($description)
    <small class="form-text text-muted">
        {{ $description }}
    </small>
@endif

<x-form.error :name="$name"/>