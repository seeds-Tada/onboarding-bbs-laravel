<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$article = new Article;
		$param = [
			'user_id'=>2,
			'name'=>"名無しのプログラマ",
			'content'=>"ようこそ掲示板へ\n次スレは>>950を踏んだ人が立ててください。",
			'reply_id'=>null,
		];
		$article->fill($param)->save();

		$param = [
			'user_id'=>2,
			'name'=>"脆弱性を突くプログラマ",
			'content'=>"<b>太字</b> / <i>斜め</i> / <u>下線</u>",
			'reply_id'=>null,
		];
		$article = new Article;
		$article->fill($param)->save();
	}
}