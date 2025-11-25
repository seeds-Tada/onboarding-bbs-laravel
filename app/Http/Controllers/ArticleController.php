<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostReplyRequest;
use App\Http\Requests\UserLoginRequest;

class ArticleController extends Controller
{
	public function index() {
		$articles = Article::query()
			->whereNull('reply_id')
			->orderBy('created_at', 'asc')
			->with([
				'user',
				'allReplies',
				'allReplies.user'
			])
			->get();

		//ログイン状態を確認する
		if(Auth::user()) {		// ログインしているユーザー
			$user_id = Auth::user()->id;
		}else {					// ログインしていないユーザー
			$user_id = null;
		}

		return view('bbs.index', ['articles'=>$articles, 'user_id'=>$user_id]);
	}

	public function login_post(UserLoginRequest $request) {
		$credentials = array(
			'email' => $request['email'],
			'password' => $request['password'],
		);

		if(Auth::guard()->attempt($credentials, $request->boolean('remember'))) {
			$request->session()->regenerate();

			session()->flash("flash.success", "You are logged in!");
			return redirect()->intended(url('/'));
		}

		session()->flash("flash.error", "Login failed!");
		return redirect('/login')->withErrors([
			'login' => 'メールアドレス又はパスワードが間違っています。',
		]);
	}

	public function post_complete(PostRequest $request) {
		$form = $request->only(['name', 'content']);
		$form += array('reply_id'=>null);
		$form += array('user_id'=>Auth::user()->id);

		$article = new Article;
		$result = $article->fill($form)->save();

		if($result) {
			session()->flash("flash.success", "登録が完了しました。");
		}else {
			session()->flash("flash.error", "登録が失敗しました。");
		}

		return redirect('/');
	}

	public function reply_post(PostReplyRequest $request) {
		$form = $request->only(['id', 'reply-name', 'reply-content']);
		$insertData = array(
			"user_id" => Auth::user()->id,
			"name" => $form['reply-name'],
			"content" => $form['reply-content'],
			"reply_id" => $form['id']
		);

		$article = new Article;
		$result = $article->fill($insertData)->save();

		if($result) {
			session()->flash("flash.success", "登録が完了しました。");
		}else {
			session()->flash("flash.error", "登録が失敗しました。");
		}

		return redirect('/');
	}

	public function editing(Request $request, Article $article) {
		// ログインしているユーザーとは別のユーザーの投稿
		if($article['user_id'] !== Auth::user()->id) {
			return redirect('/');
		}

		return view('bbs.editing', ['data'=>$article, 'id'=>$article['id']]);
	}

	public function edit_complete(PostRequest $request, Article $article) {
		$form = $request->only(['name', 'content']);

		// ログインしているユーザーとは別のユーザーの投稿
		if($article['user_id'] !== Auth::user()->id) {
			return redirect('/');
		}

		$article->name = $form['name'];
		$article->content = $form['content'];

		$result = $article->save();

		if($result) {
			session()->flash("flash.success", "編集が完了しました。");
		}else {
			session()->flash("flash.error", "編集が失敗しました。");
		}

		return redirect('/');
	}

	public function delete_complete(Request $request, Article $article) {
		// ログインしているユーザーとは別のユーザーの投稿
		if($article['user_id'] !== Auth::user()->id) {
			return redirect('/');
		}

		$result = $article->delete();

		if($result) {
			session()->flash("flash.success", "削除が完了しました。");
		}else {
			session()->flash("flash.error", "削除が失敗しました。");
		}

		return redirect('/');
	}
}