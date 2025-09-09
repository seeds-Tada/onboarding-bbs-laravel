<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>オンボーディング掲示板</title>
</head>
<body>
	<header>
		<h1>オンボーディング掲示板</h1>
	</header>
	<main>
		<div class="bbs-messages">
			@foreach ($articles as #article)
				<div class="bbs-message">
					<div class="bbs-message-header">
						{{ $article->id }}:&nbsp;<span class="bbs-message-name">{{ $article->name }}</span>&nbsp;{{ article->updated_at }}
					</div>
					<div class="bbs-message-content">
						<pre>{{ $article->content }}</pre>
					</div>
					<!-- formはまだ -->
					<div class="bbs-message-button">
						<form action="" method="post">
							<input type="hidden" name="id" value="{{ $article->id }}">
							<button type="submit">編集</button>
						</form>
						&nbsp;
						<form action="" method="post">
							<input type="hidden" name="id" value="{{ article->id }}">
							<button type="submit">削除</button>
						</form>
					</div>
				</div>
			@endforeach
		</div>
			<form action=""i method="post">
				<table class="post-table">
					<thead>
						<tr>
							<th colspan="2">新規投稿</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><label for="name">名前</label></th>
							<td><input type="text" name="name" id="name" required></td>
						</tr>
						<tr>
							<th><label for="content">投稿内容</label></th>
							<td><textarea name="content" id="content" rows="4" required></textarea></td>
						</tr>
					</tbody>
				</table>
				<button type="submit">投稿</button>
			</form>
	</main>
	<footer>
		<hr>
		<div>(b・ω・)b</div>
	</footer>
</body>
</html>