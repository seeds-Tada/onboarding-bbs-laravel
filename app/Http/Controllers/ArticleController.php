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
		function reply_push($data, $article) {
			if(is_null($article->reply_id)) {		// reply_idが0である投稿は返信ではない
				$data[$article->id] = array(
					"id" => $article->id,
					"user_id" => $article->user_id,
					"name" => $article->name,
					"content" => $article->content,
					"reply" => array(),
					"created_at" => $article->created_at,
					"updated_at" => $article->updated_at
				);
			}else {		// reply_idが0ではない投稿は返信である
				if(!empty($data[$article->reply_id])) {		// article["reply_id"]の数字がarticle["id"]と同じとき、そのidの投稿に対する返信である
						$data[$article->reply_id]["reply"][$article->id] = array(
						"id" => $article->id,
						"user_id" => $article->user_id,
						"name" => $article->name,
						"content" => $article->content,
						"reply" => array(),
						"created_at" => $article->created_at,
						"updated_at" => $article->updated_at,
					);
					return $data;
				}else {		// article["reply_id"]の数字がarticle["id"]と同じではないとき、その投稿に対する返信ではない 又は その投稿への返信に対する返信である
					foreach($data as $replyTo) {
						if(!empty($replyTo["reply"])) {		// その投稿に対する返信があるかどうか、なければその投稿への返信に対する返信ではない
							/*
								その投稿への返信に対する返信であれば、その投稿への返信に対する返信を追加したものに置き換える
								その投稿への返信に対する返信でなければ、何も変わらない（同じものと置き換える）
							*/
							$temp = reply_push($replyTo["reply"], $article);
							$data_id_temp = array_search($replyTo, $data, true);

							$replyTo["reply"] = $temp;
							$data[$data_id_temp] = $replyTo;
						}
					}
					return $data;
				}
			}
			return $data;
		}

		$articles = Article::all();
		$data = array();
		foreach($articles as $article) {
			$data = reply_push($data, $article);
		}

		//ログイン状態を確認する
		if(Auth::user()) {		// ログインしているユーザー
			$user_id = Auth::user()->id;
		}else {					// ログインしていないユーザー
			$user_id = null;
		}

		return view('bbs.index', ['articles'=>$data, 'user_id'=>$user_id]);
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