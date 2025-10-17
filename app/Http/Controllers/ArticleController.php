<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\ArticlePostRequest;

class ArticleController extends Controller
{
	public function index()
	{
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
			if ($article->reply_id === 0) {		// reply_idが0はである投稿は返信ではない
				$data[$article->id] = array(
					"id" => $article->id,
					"name" => $article->name,
					"content" => $article->content,
					"reply" => array(),
				);
			} else {		// reply_idが0はではない投稿は何らかの投稿に対する返信である
				foreach ($data as $replyTo) {
					if ($replyTo["id"] <= $article->reply_id) {		// $article["reply_id"]はデータベースでオートインクリメントされるid、そのためこれを上回るidの投稿（まだ投稿されていないもの）に対して返信することはできない
						if ($replyTo["id"] === $article->reply_id) {		// article["reply_id"]の数字がarticle["id"]と同じとき、そのidの投稿に対する返信である
							// echo ("id:" . $replyTo["id"] . " <- id:" . $article->id . " push message!<br>");
							$data[$article->reply_id]["reply"][$article->id] = array(
								"id" => $article->id,
								"name" => $article->name,
								"content" => $article->content,
								"reply" => array(),
							);
							return $data;
						} else {		// article["reply_id"]の数字がarticle["id"]と同じではないとき、その投稿に対する変死ではない 又は その投稿への返信に対する返信である
							if (!empty($replyTo["reply"])) {		// その投稿への返信があるかどうか
								/*
									その投稿への返信に対する返信であれば、その投稿への返信に対する返信を追加したものに置き換える
									その投稿への返信に対する返信でなければ、何も変わらない（同じものと置き換える）
								*/
								$temp = reply_push($replyTo["reply"], $article);

								$data_id_temp = array_search($replyTo, $data, true);
								$replyTo["reply"] = $temp;
								$data[$data_id_temp] = $replyTo;

								return $data;
							}
						}
					} else if ($replyTo["id"] > $article["reply_id"]) {
						break;
					}
				}
			}

			return $data;
		}

		$articles = Article::all();
		$data = array();
		foreach ($articles as $article) {
			$data = reply_push($data, $article);
		}

		foreach ($data as $tmp) {
			var_dump($tmp);
			echo ("<br><br>");
		}
		echo ("<br><br>");
		echo ("<br><br>");

		return view('bbs.index', ['articles' => $data]);
	}











	public function post_complete(ArticlePostRequest $request)
	{
		$form = $request->only(['name', 'content']);
		$form += array('reply_id' => 1);	//　reply_idにどの投稿に対する返信なのかを入力する

		$article = new Article;
		$result = $article->fill($form)->save();

		if ($result) {
			session()->flash("flash.success", "登録が完了しました。");
		} else {
			session()->flash("flash.error", "登録が失敗しました。");
		}


		return view('bbs.post_complete');
	}

	public function editing(Request $request, Article $article)
	{
		return view('bbs.editing', ['data' => $article, 'id' => $article['id']]);
	}

	public function edit_complete(ArticlePostRequest $request, Article $article)
	{
		$form = $request->only(['name', 'content']);

		$article->name = $form['name'];
		$article->content = $form['content'];

		$result = $article->save();

		if ($result) {
			session()->flash("flash.success", "編集が完了しました。");
		} else {
			session()->flash("flash.error", "編集が失敗しました。");
		}


		return view('bbs.edit_complete');
	}

	public function delete_confirm(Request $request, Article $article)
	{
		return view('bbs.delete_confirm', ['data' => $article, 'id' => $article['id']]);
	}

	public function delete_complete(Request $request, Article $article)
	{
		$result = $article->delete();

		if ($result) {
			session()->flash("flash.success", "削除が完了しました。");
		} else {
			session()->flash("flash.error", "削除が失敗しました。");
		}


		return view('bbs.delete_complete');
	}
}



// array(5) {
// 	[1]=> array(4) {
// 		["id"]=> int(1)
// 		["name"]=> string(27) "名無しのプログラマ"
// 		["content"]=> string(84) "ようこそ掲示板へ 次スレは>>950を踏んだ人が立ててください。"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[2]=> array(4) {
// 		["id"]=> int(2)
// 		["name"]=> string(33) "脆弱性を突くプログラマ"
// 		["content"]=> string(45) "太字 / 斜め / 下線"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[3]=> array(4) {
// 		["id"]=> int(3)
// 		["name"]=> "tester"
// 		["content"]=> string(13) "test message!"
// 		["reply"]=> array(3) {
// 			[4]=> array(4) {
// 				["id"]=> int(4)
// 				["name"]=> "tester"
// 				["content"]=> string(23) "reply message to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 			[5]=> array(4) {
// 				["id"]=> int(5)
// 				["name"]=> "tester"
// 				["content"]=> string(24) "reply message2 to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 			[6]=> array(4) {
// 				["id"]=> int(6)
// 				["name"]=> "tester"
// 				["content"]=> string(24) "reply message3 to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 		}
// 	}
// 	[7]=> array(4) {
// 		["id"]=> int(7)
// 		["name"]=> "tester"
// 		["content"]=> string(8) "test mes"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[8]=> array(4) {
// 		["id"]=> int(8)
// 		["name"]=> string(6)"tester"
// 		["content"]=> string(9) "test mes"
// 		["reply"]=> array(0) {
// 		}
// 	}
// }

// array(
// 	["id"]=> 3,
// 	["name"]=> "tester",
// 	["content"]=> "test message!",
// 	["reply"]=> array(
// 		[4]=> array(
// 			["id"]=> 4,
// 			["name"]=> "tester",
// 			["content"]=> "reply message to tester",
// 			["reply"]=> array(
// 			)
// 		),
// 		[5]=> array(
// 			["id"]=> 5,
// 			["name"]=> "tester",
// 			["content"]=> "reply message2 to tester",
// 			["reply"]=> array(
// 			)
// 		),
// 		[6]=> array(
// 			["id"]=> 6,
// 			["name"]=> "tester",
// 			["content"]=> "reply message3 to tester",
// 			["reply"]=> array(
// 				[9]=> array(
// 					["id"]=> 9,
// 					["name"] => "tester",
// 					["content"]=> "reply message to 6",
// 					["reply"]=> array(
// 					)
// 				)
// 			)
// 		)
// 	)
// );


// array(4) { ["id"]=> int(3) ["name"]=> string(6) "tester" ["content"]=> string(13) "test message!" ["reply"]=> array(3) { [4]=> array(4) { ["id"]=> int(4) ["name"]=> string(6) "tester" ["content"]=> string(23) "reply message to tester" ["reply"]=> array(0) { } } [5]=> array(4) { ["id"]=> int(5) ["name"]=> string(6) "tester" ["content"]=> string(24) "reply message2 to tester" ["reply"]=> array(0) { } } [6]=> array(4) { ["id"]=> int(6) ["name"]=> string(6) "tester" ["content"]=> string(24) "reply message3 to tester" ["reply"]=> array(0) { } } } }

// array(4) { ["id"]=> int(3) ["name"]=> string(6) "tester" ["content"]=> string(13) "test message!" ["reply"]=> array(3) { [4]=> array(4) { ["id"]=> int(4) ["name"]=> string(6) "tester" ["content"]=> string(23) "reply message to tester" ["reply"]=> array(0) { } } [5]=> array(4) { ["id"]=> int(5) ["name"]=> string(6) "tester" ["content"]=> string(24) "reply message2 to tester" ["reply"]=> array(0) { } } [6]=> array(4) { ["id"]=> int(6) ["name"]=> string(6) "tester" ["content"]=> string(24) "reply message3 to tester" ["reply"]=> array(1) { [9]=> array(4) { ["id"]=> int(9) ["name"]=> string(6) "tester" ["content"]=> string(19) "reply message to 6" ["reply"]=> array(0) { } } } } } }






// array(8) {
// 	[1]=> array(4) {
// 		["id"]=> int(1)
// 		["name"]=> string(27) "名無しのプログラマ"
// 		["content"]=> string(84) "ようこそ掲示板へ 次スレは>>950を踏んだ人が立ててください。"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[2]=> array(4) {
// 		["id"]=> int(2)
// 		["name"]=> string(33) "脆弱性を突くプログラマ"
// 		["content"]=> string(45) "太字 / 斜め / 下線"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[3]=> array(4) {
// 		["id"]=> int(3)
// 		["name"]=> string(6) "tester"
// 		["content"]=> string(13) "test message!"
// 		["reply"]=> array(3) {
// 			[4]=> array(4) {
// 				["id"]=> int(4)
// 				["name"]=> string(6) "tester"
// 				["content"]=> string(23) "reply message to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 			[5]=> array(4) {
// 				["id"]=> int(5)
// 				["name"]=> string(6) "tester"
// 				["content"]=> string(24) "reply message2 to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 			[6]=> array(4) {
// 				["id"]=> int(6)
// 				["name"]=> string(6) "tester"
// 				["content"]=> string(24) "reply message3 to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 		}
// 	}
// 	[7]=> array(4) {
// 		["id"]=> int(7)
// 		["name"]=> string(6) "tester"
// 		["content"]=> string(8) "test mes"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[8]=> array(4) {
// 		["id"]=> int(8)
// 		["name"]=> string(6) "tester"
// 		["content"]=> string(9) "test mes"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[0]=> array(4) {
// 		["id"]=> int(3)
// 		["name"]=> string(6) "tester"
// 		["content"]=> string(13) "test message!"
// 		["reply"]=> array(3) {
// 			[4]=> array(4) {
// 				["id"]=> int(4)
// 				["name"]=> string(6) "tester"
// 				["content"]=> string(23) "reply message to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 			[5]=> array(4) {
// 				["id"]=> int(5)
// 				["name"]=> string(6) "tester"
// 				["content"]=> string(24) "reply message2 to tester"
// 				["reply"]=> array(0) {
// 				}
// 			}
// 			[6]=> array(4) {
// 				["id"]=> int(6)
// 				["name"]=> string(6) "tester"
// 				["content"]=> string(24) "reply message3 to tester"
// 				["reply"]=> array(1) {
// 					[9]=> array(4) {
// 						["id"]=> int(9)
// 						["name"]=> string(6) "tester"
// 						["content"]=> string(19) "reply message to 6"
// 						["reply"]=> array(0) {
// 						}
// 					}
// 				}
// 			}
// 		}
// 	}
// 	[10]=> array(4) {
// 		["id"]=> int(10)
// 		["name"]=> string(27) "名無しのプログラマ"
// 		["content"]=> string(84) "ようこそ掲示板へ 次スレは>>950を踏んだ人が立ててください。"
// 		["reply"]=> array(0) {
// 		}
// 	}
// 	[11]=> array(4) {
// 		["id"]=> int(11)
// 		["name"]=> string(33) "脆弱性を突くプログラマ"
// 		["content"]=> string(45) "太字 / 斜め / 下線"
// 		["reply"]=> array(0) {
// 		}
// 	}
// }