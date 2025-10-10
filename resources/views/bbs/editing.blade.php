@extends('layouts.bbs')

@section('title', '投稿編集')

@section('header')
投稿編集
@endsection

@section('content')
<form action="{{ url('/edit_complete/'.$id) }}" method="post">
	@csrf
	<table class="post-table">
		<input name="id" id="id" value="{{$id}}" readonly hidden>
		<tbody>
			<tr>
				<th><label for="name">名前</label></th>
				<td><input type="text" name="name" id="name" value="{{ $data->name, old('name') }}" ></td>
			</tr>
			<tr>
				<th><label for="content">投稿内容</label></th>
				<td><textarea name="content" id="content" rows="4">{{ $data->content, old('content') }}</textarea></td>
			</tr>
		</tbody>
	</table>
	<button type="submit">編集</button>
</form>

@if (count($errors) > 0)
	<div class="error-mes">
		@foreach ($errors->all() as $error)
			<span>{{$error}}</span>
		@endforeach
	</div>
@endif
@endsection

@section('footer')
＿φ(・ω・  )
@endsection