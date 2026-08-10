```php
<?php

return [

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeには英字のみ使用できます。',
    'alpha_dash' => ':attributeには英字、数字、ハイフン、アンダースコアのみ使用できます。',
    'alpha_num' => ':attributeには英字と数字のみ使用できます。',
    'array' => ':attributeには配列を指定してください。',
    'ascii' => ':attributeにはASCII文字のみ使用できます。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',

    'between' => [
        'array' => ':attributeには:min～:max個の項目を指定してください。',
        'file' => ':attributeは:min～:maxキロバイトである必要があります。',
        'numeric' => ':attributeには:min～:maxの値を指定してください。',
        'string' => ':attributeは:min～:max文字である必要があります。',
    ],

    'boolean' => ':attributeにはtrueまたはfalseを指定してください。',
    'can' => ':attributeには許可されていない値が含まれています。',
    'confirmed' => ':attributeと確認用の値が一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeには有効な日付を指定してください。',
    'date_equals' => ':attributeには:dateと同じ日付を指定してください。',
    'date_format' => ':attributeは:format形式と一致しません。',
    'decimal' => ':attributeには:decimal桁の小数を指定してください。',
    'declined' => ':attributeは拒否する必要があります。',
    'declined_if' => ':otherが:valueの場合、:attributeは拒否する必要があります。',
    'different' => ':attributeと:otherには異なる値を指定してください。',
    'digits' => ':attributeには:digits桁の数字を指定してください。',
    'digits_between' => ':attributeには:min～:max桁の数字を指定してください。',
    'dimensions' => ':attributeの画像サイズが正しくありません。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeは次の値で終わることはできません: :values。',
    'doesnt_start_with' => ':attributeは次の値で始めることはできません: :values。',
    'email' => ':attributeには有効なメールアドレスを指定してください。',
    'ends_with' => ':attributeは次のいずれかで終わる必要があります: :values。',
    'enum' => '選択した:attributeは無効です。',
    'exists' => '選択した:attributeは無効です。',
    'extensions' => ':attributeには次の拡張子のファイルを指定してください: :values。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeには値を入力してください。',
    'gt' => [
        'array' => ':attributeには:value個より多くの項目が必要です。',
        'file' => ':attributeは:valueキロバイトより大きい必要があります。',
        'numeric' => ':attributeは:valueより大きい必要があります。',
        'string' => ':attributeは:value文字より長い必要があります。',
    ],

    'gte' => [
        'array' => ':attributeには:value個以上の項目が必要です。',
        'file' => ':attributeは:valueキロバイト以上である必要があります。',
        'numeric' => ':attributeは:value以上である必要があります。',
        'string' => ':attributeは:value文字以上である必要があります。',
    ],

    'hex_color' => ':attributeには有効な16進数カラーコードを指定してください。',
    'image' => ':attributeには画像ファイルを指定してください。',
    'in' => '選択した:attributeは無効です。',
    'in_array' => ':attributeが:otherに存在しません。',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',

    'lowercase' => ':attributeには小文字のみ使用できます。',

    'lt' => [
        'array' => ':attributeには:value個より少ない項目が必要です。',
        'file' => ':attributeは:valueキロバイトより小さい必要があります。',
        'numeric' => ':attributeは:valueより小さい必要があります。',
        'string' => ':attributeは:value文字より短い必要があります。',
    ],

    'lte' => [
        'array' => ':attributeには:value個以下の項目を指定してください。',
        'file' => ':attributeは:valueキロバイト以下である必要があります。',
        'numeric' => ':attributeは:value以下である必要があります。',
        'string' => ':attributeは:value文字以下である必要があります。',
    ],

    'mac_address' => ':attributeには有効なMACアドレスを指定してください.',

    'max' => [
        'array' => ':attributeには:max個以下の項目を指定してください。',
        'file' => ':attributeは:maxキロバイト以下である必要があります。',
        'numeric' => ':attributeは:max以下である必要があります。',
        'string' => ':attributeは:max文字以下である必要があります。',
    ],

    'max_digits' => ':attributeは:max桁以下である必要があります。',
    'mimes' => ':attributeには次の形式のファイルを指定してください: :values。',
    'mimetypes' => ':attributeには次のMIMEタイプのファイルを指定してください: :values。',

    'min' => [
        'array' => ':attributeには:min個以上の項目が必要です。',
        'file' => ':attributeは:minキロバイト以上である必要があります。',
        'numeric' => ':attributeは:min以上である必要があります。',
        'string' => ':attributeは:min文字以上である必要があります。',
    ],

    'min_digits' => ':attributeは:min桁以上である必要があります。',
    'missing' => ':attributeを指定しないでください。',
    'missing_if' => ':otherが:valueの場合、:attributeを指定しないでください。',
    'missing_unless' => ':otherが:valuesでない場合、:attributeを指定しないでください。',
    'missing_with' => ':valuesが存在する場合、:attributeを指定しないでください。',
    'missing_with_all' => ':valuesがすべて存在する場合、:attributeを指定しないでください。',
    'multiple_of' => ':attributeは:valueの倍数である必要があります。',
    'not_in' => '選択した:attributeは無効です。',
    'not_regex' => ':attributeの形式が正しくありません。',
    'numeric' => ':attributeには数字を指定してください。',
    'password' => 'パスワードが正しくありません。',
    'present' => ':attributeを指定してください。',
    'present_if' => ':otherが:valueの場合、:attributeを指定してください。',
    'present_unless' => ':otherが:valuesでない場合、:attributeを指定してください。',
    'present_with' => ':valuesが存在する場合、:attributeを指定してください。',
    'present_with_all' => ':valuesがすべて存在する場合、:attributeを指定してください。',
    'prohibited' => ':attributeを指定することはできません。',
    'prohibited_if' => ':otherが:valueの場合、:attributeを指定することはできません。',
    'prohibited_unless' => ':otherが:valuesでない限り、:attributeを指定することはできません。',
    'prohibits' => ':attributeは:otherが存在する場合に指定できません。',

    'regex' => ':attributeの形式が正しくありません。',
    'required' => ':attributeは必須です。',
    'required_if' => ':otherが:valueの場合、:attributeは必須です。',
    'required_if_accepted' => ':otherが承認されている場合、:attributeは必須です。',
    'required_unless' => ':otherが:valuesでない限り、:attributeは必須です。',
    'required_with' => ':valuesのいずれかが存在する場合、:attributeは必須です。',
    'required_with_all' => ':valuesがすべて存在する場合、:attributeは必須です。',
    'required_without' => ':valuesのいずれも存在しない場合、:attributeは必須です。',
    'required_without_all' => ':valuesがすべて存在しない場合、:attributeは必須です。',

    'same' => ':attributeと:otherは同じ値である必要があります。',
    'size' => [
        'array' => ':attributeには:size個の項目が必要です。',
        'file' => ':attributeは:sizeキロバイトである必要があります。',
        'numeric' => ':attributeは:sizeである必要があります。',
        'string' => ':attributeは:size文字である必要があります。',
    ],

    'starts_with' => ':attributeは次のいずれかで始まる必要があります: :values。',
    'string' => ':attributeには文字列を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => ':attributeはすでに使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeには大文字のみ使用できます。',
    'url' => ':attributeには有効なURLを指定してください。',
    'ulid' => ':attributeには有効なULIDを指定してください。',
    'uuid' => ':attributeには有効なUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'カスタムエラーメッセージ。',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
        'phone' => '電話番号',
        'address' => '住所',

        'department_id' => '部署',
        'position_id' => '役職',
        'employee_id' => '社員',
        'employee_code' => '社員番号',

        'contract_type' => '契約形態',
        'start_date' => '開始日',
        'end_date' => '終了日',

        'date' => '日付',
        'title' => 'タイトル',
        'description' => '説明',
        'content' => '内容',
        'status' => 'ステータス',
        'type' => 'タイプ',

        'image' => '画像',
        'file' => 'ファイル',
        'avatar' => 'プロフィール画像',

        'current_password' => '現在のパスワード',
        'new_password' => '新しいパスワード',

        'parent_id' => '親項目',
        'sort_order' => '並び順',
    ],

];