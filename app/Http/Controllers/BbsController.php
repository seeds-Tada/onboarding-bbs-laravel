<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class BbsController extends Controller
{
	public function index() {
		$articles = array(
			[
				'id'=>1,
				'name'=>'名無しのプログラマ',
				'content'=>'ようこそ掲示板へ\n次スレは>>950を踏んだ人が立ててください。',
				'updated_at'=>'today'
			],
			[
				'id'=>2,
				'name'=>'脆弱性を突くプログラマ',
				'content'=>'<b>太字</b> / <i>斜め</i> / <u>下線</u>',
				'updated_at'=>'today'
			]
		);

		// return view('bbs.index', ['articles' => $articles]);

		$articles = Article::all();
		return view('bbs.index', ['articles' => $articles->toArray()]);
	}

	public function post_confirm(Request $request) {
		$form = $request->all();
		return view('bbs.post_confrim', ['data' => $form]);
		// return view('bbs.post_confrim', ['data' => var_export($form, true)]);
	}
}
