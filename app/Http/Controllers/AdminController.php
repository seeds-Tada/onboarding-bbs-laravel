<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\PostReplyRequest;
use App\Http\Requests\AdminCreateUserRequest;
use App\Http\Requests\AdminCreateAdminRequest;
use App\Http\Requests\AdminEditUserRequest;
use App\Http\Requests\AdminEditAdminRequest;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use App\Models\User;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
	public function login_get(Request $request) {
		return view('bbs_admin.login');
	}

	public function login_post(AdminLoginRequest $request) {
		$credentials = array(
			'admin_email' => $request['admin_email'],
			'password' => $request['password'],
		);

		if(Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
			$request->session()->regenerate();
			return redirect()->intended(url('/admin/index'));
		}

		return redirect('/admin/login')->withErrors([
			'login' => 'メールアドレス又はパスワードが間違っています。',
		]);
	}

	public function logout(Request $request) {
		Auth::guard('admin')->logout();
		return redirect('/admin/login');
	}

	// 掲示板管理画面
	public function index(Request $request) {
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

	public function post(PostRequest $request) {
		$form = $request->only(['name', 'content']);
		$form += array('reply_id'=>0);
		$form += array('user_id'=>0);

		$article = new Article;
		$result = $article->fill($form)->save();

		if($result) {
			session()->flash("flash.success", "登録が完了しました。");
		}else {
			session()->flash("flash.error", "登録が失敗しました。");
		}

		return redirect('/admin/index');
	}

	public function reply_post(PostReplyRequest $request) {
		$form = $request->only(['id', 'reply-name', 'reply-content']);
		$insertData = array(
			"user_id" => 0,
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

		return redirect('/admin/index');
	}

	public function editing(Request $request, Article $article) {
		return view('bbs_admin.editing', ['data'=>$article, 'id'=>$article['id']]);
	}

	public function edit_complete(PostRequest $request, Article $article) {
		$form = $request->only(['name', 'content']);

		$article->name = $form['name'];
		$article->content = $form['content'];
		$result = $article->save();

		if($result) {
			session()->flash("flash.success", "編集が完了しました。");
		}else {
			session()->flash("flash.error", "編集が失敗しました。");
		}

		return redirect('/admin/index');
	}

	public function delete_complete(Request $request, Article $article) {
		$result = $article->delete();

		if($result) {
			session()->flash("flash.success", "削除が完了しました。");
		}else {
			session()->flash("flash.error", "削除が失敗しました。");
		}

		return redirect('/admin/index');
	}

	// ユーザー管理画面
	public function users(Request $request) {
		$admins = Admin::all();
		$adminsData = array();
		foreach($admins as $admin) {
			array_push(
				$adminsData,
				array(
					"id" => $admin->id,
					"name" => $admin->admin_name,
					"email" => $admin->admin_email,
					"created_at" => $admin->created_at,
					"updated_at" => $admin->updated_at,
				)
			);
		}

		$users = User::all();
		$usersData = array();
		foreach($users as $user) {
			array_push(
				$usersData,
				array(
					"id" => $user->id,
					"name" => $user->name,
					"email" => $user->email,
					"created_at" => $user->created_at,
					"updated_at" => $user->updated_at,
				)
			);
		}

		return view('bbs_admin.users', ['adminsData'=>$adminsData, 'usersData'=>$usersData]);
	}

	public function admin_create(AdminCreateAdminRequest $request) {
		$form = $request->only(['admin_name', 'admin_email', 'password']);
		$form['password'] = Hash::make($form['password']);

		$admin = new Admin;
		$result = $admin->fill($form)->save();

		if($result) {
			session()->flash("flash.success", "管理者アカウントの作成が完了しました。");
		}else {
			session()->flash("flash.error", "管理者アカウントの作成が失敗しました。");
		}

		return redirect('/admin/users');
	}

	public function admin_edit(Request $request, Admin $article) {
		return view('bbs_admin.user_edit', ['data'=>$article, 'id'=>$article['id'], 'role'=>'admin']);
	}

	public function admin_edit_complete(AdminEditAdminRequest $request, Admin $article) {
		$form = $request->only(['admin_name', 'admin_email']);

		$article->admin_name = $form['admin_name'];
		$article->admin_email = $form['admin_email'];
		$result = $article->save();

		if($result) {
			session()->flash("flash.success", "管理者ユーザーの編集が完了しました。");
		}else {
			session()->flash("flash.error", "管理者ユーザーの編集が失敗しました。");
		}

		return redirect('/admin/users');
	}

	public function admin_delete_confirm(Request $request, Admin $article) {
		return view('bbs_admin.user_delete_confirm', ['data'=>$article, 'id'=>$article['id'], 'role'=>'admin']);
	}

	public function admin_delete_complete(Request $request, Admin $article) {
		$result = $article->delete();

		if($result) {
			session()->flash("flash.success", "管理者ユーザーの削除が完了しました。");
		}else {
			session()->flash("flash.error", "管理者ユーザーの削除が失敗しました。");
		}
		return redirect('/admin/users');
	}

	public function user_create(AdminCreateUserRequest $request) {
		$form = $request->only(['name', 'email', 'password']);
		$form['password'] = Hash::make($form['password']);

		$user = new User;
		$result = $user->fill($form)->save();

		if($result) {
			session()->flash("flash.success", "一般アカウントの作成が完了しました。");
		}else {
			session()->flash("flash.error", "一般アカウントの作成が失敗しました。");
		}

		return redirect('/admin/users');
	}

	public function user_edit(Request $request, User $article) {
		return view('bbs_admin.user_edit', ['data'=>$article, 'id'=>$article['id'], 'role'=>'user']);
	}

	public function user_edit_complete(AdminEditUserRequest $request, User $article) {
		$form = $request->only(['name', 'email']);

		$article->name = $form['name'];
		$article->email = $form['email'];
		$result = $article->save();

		if($result) {
			session()->flash("flash.success", "一般ユーザーの編集が完了しました。");
		}else {
			session()->flash("flash.error", "一般ユーザーの編集が失敗しました。");
		}

		return redirect('/admin/users');
	}

	public function user_delete_confirm(Request $request, User $article) {
		return view('bbs_admin.user_delete_confirm', ['data'=>$article, 'id'=>$article['id'], 'role'=>'user']);
	}

	public function user_delete_complete(Request $request, User $article) {
		$result = $article->delete();

		if($result) {
			session()->flash("flash.success", "一般ユーザーの削除が完了しました。");
		}else {
			session()->flash("flash.error", "一般ユーザーの削除が失敗しました。");
		}

		return redirect('/admin/users');
	}
}