@extends('layouts.bbs')

@section('title', '投稿確認')

@section('header')
確認
@endsection

@section('content')
<div>下記の内容で投稿しますがよろしいですか？</div>
<table class="post-table">
	<tbody>
	<tr><th>名前</th><td><span>{{ $data['name'] }}</span></td></tr>
	<tr><th>投稿内容</th><td><span><pre>{{ $data['content'] }}</pre></span></td></tr>
	</tbody>
</table>
<form action="{{ url('/') }}" method="post">
	@csrf
	<button type="submit">投稿</button>
</form>
@endsection

@section('footer')
_〆(・ω・;)
@endsection