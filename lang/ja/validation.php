<?php

return [
	'required' => ':attribute は必須です。',
	'string' => ':attribute は文字列である必要があります。',
	'integer' => ':attribute は整数である必要があります。',
	'email' => ':attribute はメールアドレスである必要があります。',
	'max' => [
		'string' => ':attribute は :max 以内である必要があります。',
	],
	'min' => [
		'string' => ':attribute は :min 文字以上である必要があります。',
	],
	'unique' => ':attribute はすでに登録されています。',
	'attributes' => [
		'name' => '名前',
		'admin_name' => '名前',
		'content' => '内容',
		'email' => 'メールアドレス',
		'admin_email' => 'メールアドレス',
		'password' => 'パスワード',
		'id' => '投稿ID',
		'reply-name' => '名前',
		'reply-content' => '内容'
	],
];