@extends('layouts.bbs')

@section('title', 'オンボーディング掲示板')

@section('header')
オンボーディング掲示板
@endsection

@section('content')
<div class="bbs-messages">
	@foreach ($articles as $article)
		<div class="bbs-message">
			<div class="bbs-message-header">
				{{ $article->id }}:&nbsp;<span class="bbs-message-name">{{ $article->name }}</span>&nbsp;{{ $article->updated_at }}
			</div>
			<div class="bbs-message-content">
				<pre>{{ $article->content }}</pre>
			</div>
			<div class="bbs-message-button">
				<form action="{{ url('/editing/'.$article->id) }}" method="post">
					@csrf
					<input type="hidden" name="id" value="{{ $article->id }}">
					<button type="submit">編集</button>
				</form>
				&nbsp;
				<form action="{{ url('/delete_confirm/'.$article->id) }}" method="post">
					@csrf
					<input type="hidden" name="id" value="{{ $article->id }}">
					<button type="submit">削除</button>
				</form>
			</div>
		</div>
		<br />
	@endforeach
</div>
<form action="{{ url('/post_confirm') }}" method="post">
	@csrf
	<table class="post-table">
		<thead>
			<tr>
				<th colspan="2">新規投稿</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><label for="name">名前</label></th>
				<td><input type="text" name="name" id="name"></td>
			</tr>
			<tr>
				<th><label for="content">投稿内容</label></th>
				<td><textarea name="content" id="content" rows="4"></textarea></td>
			</tr>
		</tbody>
	</table>
	<button type="submit">投稿</button>
</form>
@endsection

@section('footer')
(b・ω・)b
@endsection