@extends('layouts.bbs')

@section('title', '投稿確認')

@section('header')
確認
@endsection

@section('content')
<div>下記の内容で投稿しますがよろしいですか？</div>
<form action="{{ url('/post_complete') }}" method="post">
	@csrf
	<table class="post-table">
		<tbody>
			<tr>
				<th>名前</th>
				<td><input type="text" name="name" id="name" value="{{ $data['name'] }}" readonly></td>
			</tr>
			<tr>
				<th>投稿内容</th>
				<td><textarea name="content" id="content" rows="4" readonly>{{ $data['content'] }}</textarea></td>
			</tr>
		</tbody>
	</table>
	<button type="submit">投稿</button>
</form>
@endsection

@section('footer')
_〆(・ω・;)
@endsection