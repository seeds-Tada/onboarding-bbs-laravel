<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleUpdateRequest;

class BbsController extends Controller
{
	public function index() {
		$articles = Article::all();

		return view('bbs.index', ['articles' => $articles]);
	}

	public function post_confirm(ArticleCreateRequest $request) {
		$form = $request->all();
		unset($form['_token']);


		$request->session()->put('name', $form['name']);
		$request->session()->put('content', $form['content']);
		
		return view('bbs.post_confirm', ['data' => $form]);
	}

	public function post_complete(Request $request) {
		$form = $request->all();
		unset($form['_token']);

		$name = $request->session()->get('name');
		$content = $request->session()->get('content');
		$request->session()->forget('name');
		$request->session()->forget('content');
		$post = array(
			'name' => $name,
			'content' => $content
		);
		if(!$post['name'] || !$post['content']) {
			return redirect('/');
		}

		$article = new Article;
		$article->fill($post)->save();

		return view('bbs.post_complete');
	}

	public function editing(ArticleUpdateRequest $request) {
		$form = $request->all();
		unset($form['_token']);

		$request->session()->put('id', $request->id);

		$article = Article::find($request->id);
		if(!$article) {
			$request->session()->forget('id');
			return redirect('/');
		}

		return view('bbs.editing', ['data' => $article]);
	}

	public function edit_complete (ArticleCreateRequest $request) {
		$form = $request->all();
		unset($form['_token']);
		if(!$form['name'] || !$form['content']) {
			$request->session()->forget('id');
			return redirect('/');
		}

		$id = $request->session()->get('id');
		$request->session()->forget('id');
		if(!$id) {
			return redirect('/');
		}

		$article = Article::find($id);
		$article->name = $form['name'];
		$article->content = $form['content'];
		$article->save();

		return view('bbs.edit_complete');
	}

	public function delete_confirm (Request $request) {
		$form = $request->all();
		unset($form['_token']);

		if(!$request->id) {
			return redirect('/');
		}
		$request->session()->put('id', $request->id);

		$article = Article::find($request->id);
		if(!$article) {
			$request->session()->forget('id');
			return redirect('/');
		}

		return view('bbs.delete_confirm', ['data' => $article]);
	}

	public function delete_complete (Request $request) {
		$form = $request->all();
		unset($form['_token']);

		$id = $request->session()->get('id');
		$request->session()->forget('id');
		if(!$id) {
			return redirect('/');
		}

		$article = Article::find($id)->delete();

		return view('bbs.delete_complete');
	}
}