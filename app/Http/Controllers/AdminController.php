<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login_get(Request $request) {
        return view('bbs_admin.login');
    }

    public function login_post(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        echo("waaaa!!!!");

        if(Auth::guard('admin')->attempt($credentials, $request->boolean('rememder'))) {
            $request->session()->regenerate();
            return redirect()->intended(url('/admin/index'));
        }

        echo("not login.");

        return view('bbs_admin.login');
        // return redirect('/admin/login');
    }

    public function index(Request $request) {
		//ユーザーを確認する
		if(Auth::guard('admin')->user()) {						// ログインしているユーザー
			if(Auth::guard('admin')->check() !== true) {
				return redirect('/admin/login');
			}
		}else {									// ログインしていないユーザー
			return redirect('/admin/login');
		}

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

		return view('bbs_admin.index', ['articles'=>$data]);
    }
}
