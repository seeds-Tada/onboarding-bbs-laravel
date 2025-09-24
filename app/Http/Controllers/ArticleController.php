<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleUpdateRequest;

class ArticleController extends Controller
{
	public function index() {
		$articles = Article::all();

		return view('bbs.index', compact('articles'));
	}

	public function post_confirm(ArticleCreateRequest $request) {
		$data = $request->only(['name', 'content']);

		return view('bbs.post_confirm', compact('data'));
	}

	public function post_complete(ArticleCreateRequest $request) {
		$form = $request->only(['name', 'content']);

		$article = new Article;
		$article->fill($form)->save();

		return view('bbs.post_complete');
	}

	public function editing(ArticleUpdateRequest $request) {
		$form = $request->only(['id']);

		$id = $form['id'];
		$data = Article::find($id);
		if(!$data) {
			return redirect('/');
		}

		return view('bbs.editing', compact('data', 'id'));
	}

	public function edit_complete (ArticleCreateRequest $request) {
		$form = $request->only(['name', 'content', 'id']);
		if(!$form['id']) {
			return redirect('/');
		}

		$id = $form['id'];
		$article = Article::find($id);
		$article->name = $form['name'];
		$article->content = $form['content'];
		$article->save();

		return view('bbs.edit_complete');
	}

	public function delete_confirm (ArticleUpdateRequest $request) {
		$form = $request->all();
		$form = $request->only(['id']);

		$id = $form['id'];
		$data = Article::find($id);
		if(!$data) {
			return redirect('/');
		}

		return view('bbs.delete_confirm', compact('data', 'id'));
	}

	public function delete_complete (ArticleUpdateRequest $request) {
		$form = $request->only(['id']);

		$id = $form['id'];
		$article = Article::find($id)->delete();

		return view('bbs.delete_complete');
	}
}