<details>
	<summary>
		<strong class="index-reply-switch">{{$to['id']}}への返信を表示する</strong>
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
					<form action="{{ url('/') }}" method="get">
						@csrf
						<input type="hidden" name="id" value="{{ $article['id'] }}">
						<button type="submit">返信</button>
					</form>
				</div>
				@if ($article['reply'])
					<div>
						@include(
							"components/index_reply",
							[
								"articles" => $article["reply"],
								"to" => $article
							]
						)
					</div>
				@endif
			</div>
		@endforeach
	</div>
</details>