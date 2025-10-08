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

	public function post_confirm(ArticlePostRequest $request) {
		$data = $request->only(['name', 'content']);

		return view('bbs.post_confirm', compact('data'));
	}

	public function post_complete(ArticlePostRequest $request) {
		$form = $request->only(['name', 'content']);

		$article = new Article;
		$article->fill($form)->save();

		return view('bbs.post_complete');
	}

	public function editing(Request $request, Article $article) {
		if(!$article) {
			return redirect('/');
		}

		$data = $article;
		$id = $article['id'];
		return view('bbs.editing', compact('data', 'id'));
	}

	public function edit_complete (ArticlePostRequest $request, Article $article) {
		$form = $request->only(['name', 'content']);

		$article->name = $form['name'];
		$article->content = $form['content'];
		$article->save();

		return view('bbs.edit_complete');
	}

	public function delete_confirm (Request $request, Article $article) {
		if(!$article) {
			return redirect('/');
		}

		$data = $article;
		$id = $article['id'];
		return view('bbs.delete_confirm', compact('data', 'id'));
	}

	public function delete_complete (Request $request, Article $article) {		
		$article->delete();

		return view('bbs.delete_complete');
	}
}