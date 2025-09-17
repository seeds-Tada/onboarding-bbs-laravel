@extends('layouts.bbs')

@section('title', '投稿編集')

@section('header')
投稿編集
@endsection

@section('content')
<form action="{{ url('/edit_complete') }}" method="post">
	@csrf
	<table class="post-table">
		<tbody>
		<tr>
			<th><label for="name">名前</label></th>
			<td><input type="text" name="name" id="name" value="{{ $data->name }}" ></td>
		</tr>
		<tr>
			<th><label for="content">投稿内容</label></th>
			<td><textarea name="content" id="content" rows="4">{{ $data->content }}</textarea></td>
		</tr>
		</tbody>
	</table>
	<button type="submit">編集</button>
</form>
@endsection

@section('footer')
＿φ(・ω・  )
@endsection