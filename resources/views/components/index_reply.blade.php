<details>
	<summary>
		<strong class="index-reply-switch">{{$to['id']}}への返信を表示する {{count($to['reply'])}}件</strong>
	</summary>
	<div>
		@foreach ($articles as $article)
			<hr>
			<div  id="index-message-{{$article['id']}}">
				<div class="bbs-message-header">
					{{ $article['id'] }}:&nbsp;<span class="bbs-message-name">{{ $article['name'] }}</span>&nbsp;{{$article['updated_at']}}
				</div>
				<div class="bbs-message-content">
					<pre><a class="index-reply-to" href="#index-message-{{$to['id']}}">>>{{$to['id']}}</a>{{ $article['content'] }}</pre>
				</div>
				@if( $article['user_id'] === $user_id )
					<div class="bbs-message-button">
						<form action="{{ url('/editing/'.$article['id']) }}" method="post">
							@csrf
							<input type="hidden" name="id" value="{{ $article['id'] }}">
							<button type="submit">編集</button>
						</form>
						&nbsp;
						<form action="{{ url('/delete_confirm/'.$article['id']) }}" method="post">
							@csrf
							<input type="hidden" name="id" value="{{ $article['id'] }}">
							<button type="submit">削除</button>
						</form>
					</div>
				@endif
				<details>
					<summary>
						<strong class="reply-post-switch">返信する</strong>
					</summary>
					<div>
						<form action="{{ url('/reply_post') }}" method="post" class="reply-post-form">
							@csrf
							<input type="hidden" name="id" value="{{ $article['id'] }}">
							<table class="reply-post-table">
								<tbody>
									<tr>
										<th><label for="reply-name-{{ $article['id'] }}">名前</label></th>
										<td><input type="text" name="reply-name-{{ $article['id'] }}" id="reply-name-{{ $article['id'] }}" value="{{ old('reply-name-'.$article['id']) }}"></td>
									</tr>
									<tr>
									<th><label for="reply-content-{{ $article['id'] }}">投稿内容</label></th>
										<td>
											<textarea name="reply-content-{{ $article['id'] }}" id="reply-content-{{ $article['id'] }}" rows="4">{{ old("reply-content-".$article['id']) }}</textarea>
										</td>
									</tr>
								</tbody>
							</table>
							<button type="submit">返信</button>
						</form>
					</div>
				</details>
				@if ($article['reply'])
					<div>
						@include(
							"components/index_reply",
							[
								"articles" => $article["reply"],
								"to" => $article,
								"user_id" => $user_id
							]
						)
					</div>
				@endif
			</div>
		@endforeach
	</div>
</details>