<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\ArticlePostRequest;

class ArticleController extends Controller
{
	public function index() {
		$articles = Article::all();

		return view('bbs.index', compact('articles'));
	}

	public function post_complete(ArticlePostRequest $request) {
		$form = $request->only(['name', 'content']);

		$article = new Article;
		$result = $article->fill($form)->save();

		if($result) {
			session()->flash("flash.success", "登録が完了しました。");
		}else {
			session()->flash("flash.error", "登録が失敗しました。");
		}


		return view('bbs.post_complete');
	}

	public function editing(Request $request, Article $article) {
		return view('bbs.editing', ['data'=>$article, 'id'=>$article['id']]);
	}

	public function edit_complete (ArticlePostRequest $request, Article $article) {
		$form = $request->only(['name', 'content']);

		$article->name = $form['name'];
		$article->content = $form['content'];

		$result = $article->save();

		if($result) {
			session()->flash("flash.success", "編集が完了しました。");
		}else {
			session()->flash("flash.error", "編集が失敗しました。");
		}


		return view('bbs.edit_complete');
	}

	public function delete_confirm (Request $request, Article $article) {
		return view('bbs.delete_confirm', ['data'=>$article, 'id'=>$article['id']]);
	}

	public function delete_complete (Request $request, Article $article) {		
		$result = $article->delete();

		if($result) {
			session()->flash("flash.success", "削除が完了しました。");
		}else {
			session()->flash("flash.error", "削除が失敗しました。");
		}


		return view('bbs.delete_complete');
	}
}