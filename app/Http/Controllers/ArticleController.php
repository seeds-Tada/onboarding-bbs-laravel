<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ArticlePostRequest;

class ArticleController extends Controller
{
	public function index() {
		function reply_push($data, $article) {
			/*
				木構造を配列で表現する
				木構造配列
				array(		// すべての投稿
					array(		// 投稿
						"id" => 1,
						"name" => "tester",
						"content" => "new post1",
						"reply" => array(),　　// 返信があった場合、ここに返信の投稿を追加する
					),
					array(
						"id" => 2,
						"name" => "tester",
						"content" => "new post2",
						"reply" => array(
							array(		// 投稿に対するの返信
								"id" => 3,
								"name" => "tester",
								"content" => "reply post1",
								"reply" => array(),
							),
							array(
								"id" => 4,
								"name" => "tester",
								"content" => "reply post",
								"reply" => array(
									array(		// 投稿への返信に対する返信
										"id" => 5,
										"name" => "tester",
										"content" => "reply post3",
										"reply" => array(),
									),
								),
							),
						),
					),
				)
			
				article table		(上記の木構造で表現されたデータの場合)
				id,		name,		content,			reply_id
				1,		"tester",	"new post1",		0
				2,		"tester",	"new post2",		0
				3,		"tester",	"reply post1",		2
				4,		"tester",	"reply post2",		2
				5,		"tester",	"reply post3",		4

				どの投稿に対する返信なのかをreply_idに保存する
				reply_idが0である投稿は返信ではない
			*/
			if($article->reply_id === 0) {		// reply_idが0である投稿は返信ではない
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

	public function post_complete(ArticlePostRequest $request) {
		$form = $request->only(['name', 'content']);
		$form += array('reply_id'=>0);

		$article = new Article;
		$result = $article->fill($form)->save();

		if($result) {
			session()->flash("flash.success", "登録が完了しました。");
		}else {
			session()->flash("flash.error", "登録が失敗しました。");
		}

		return redirect('/');
	}

	public function reply_post(Request $request) {
		$form = $request->only(['id']);
		if(empty($form['id'])) {
			return redirect('/');
		}

		$form = $request->only(['reply-name-'.$form['id'], 'reply-content-'.$form['id'], 'id']);
		if(empty($form['reply-name-'.$form['id']]) || empty($form['reply-content-'.$form['id']])) {
			return redirect('/');
		}

		$form = array(
			"name" => $form['reply-name-'.$form['id']],
			"content" => $form['reply-content-'.$form['id']],
			"reply_id" => $form['id']
		);

		$article = new Article;
		$result = $article->fill($form)->save();

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

	public function edit_complete(ArticlePostRequest $request, Article $article) {
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