@extends('layouts.bbs_admin')

@section('title', '管理者オンボーディング掲示板')

@section('content')
<br>
	@if( session("flash") )
		@foreach( session("flash") as $key => $item )
			<div class="flash-alert flash-alert-{{ $key }}">
				{{ session("flash.".$key) }}
			</div>
		@endforeach
	@endif
<div class="bbs-messages">
	@foreach ($articles as $article)
		<div class="bbs-message" id="index-message-{{$article['id']}}">
			<div class="bbs-message-header">
				{{ $article['id'] }}:&nbsp;<span class="bbs-message-name">{{ $article['name'] }}</span>&nbsp;{{$article['updated_at']}}
			</div>
			<div class="bbs-message-content">
				<pre>{{ $article['content'] }}</pre>
			</div>
			<div class="bbs-message-button">
				<form action="{{ url('/admin/editing/'.$article['id']) }}" method="post">
					@csrf
					<input type="hidden" name="id" value="{{ $article['id'] }}">
					<button type="submit">編集</button>
				</form>
				&nbsp;
				<form action="{{ url('/admin/delete_complete/'.$article['id']) }}" method="post">
					@csrf
					<input type="hidden" name="id" value="{{ $article['id'] }}">
					<button type="submit">削除</button>
				</form>
			</div>
			<details>
				<summary>
					<strong class="reply-post-switch">返信する</strong>
				</summary>
				<div>
					<form action="{{ url('/admin/reply_post') }}" method="post" class="reply-post-form">
						@csrf
						<input type="hidden" name="id" value="{{ $article['id'] }}">
						<table class="reply-post-table">
							<tbody>
								<tr>
									<th><label for="reply-name-{{ $article['id'] }}">名前</label></th>
									<td><input type="text" name="reply-name" id="reply-name-{{ $article['id'] }}" value="" required></td>
								</tr>
								<tr>
									<th><label for="reply-content-{{ $article['id'] }}">投稿内容</label></th>
									<td>
										<textarea name="reply-content" id="reply-content-{{ $article['id'] }}" rows="4" required></textarea>
									</td>
								</tr>
							</tbody>
						</table>
						<button type="submit">返信</button>
					</form>
					@if (count($errors) > 0 && old('id') == $article['id'])
						<div class="error-mes">
							@foreach ($errors->all() as $error)
								<span>{{$error}}</span>
							@endforeach
						</div>
					@endif
				</div>
			</details>
			@if ($article['reply'])
				<div>
					@include(
						"components/admin_index_reply",
						[
							"articles" => $article["reply"],
							"to" => $article
						]
					)
				</div>
			@endif
		</div>
		<br />
	@endforeach
</div>
<form action="{{ url('/admin/post') }}" method="post">
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
				<td><input type="text" name="name" id="name" value="{{ old('name') }}" required></td>
			</tr>
			<tr>
				<th><label for="content">投稿内容</label></th>
				<td><textarea name="content" id="content" rows="4" required> {{ old('content') }}</textarea></td>
			</tr>
		</tbody>
	</table>
	<button type="submit">投稿</button>
</form>

@if (count($errors) > 0 && !old('id'))
	<div class="error-mes">
		@foreach ($errors->all() as $error)
			<span>{{$error}}</span>
		@endforeach
	</div>
@endif
@endsection

@section('footer')
(b・ω・)b
@endsection