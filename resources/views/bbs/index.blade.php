@extends('layouts.bbs')

@section('title', 'オンボーディング掲示板')

@section('header')
オンボーディング掲示板
@endsection

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
</div>
<form action="{{ url('/post_complete') }}" method="post">
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
				<td><input type="text" name="name" id="name" value="{{ old('name') }}"></td>
			</tr>
			<tr>
				<th><label for="content">投稿内容</label></th>
				<td><textarea name="content" id="content" rows="4"> {{ old('content') }}</textarea></td>
			</tr>
		</tbody>
	</table>
	<button type="submit">投稿</button>
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
(b・ω・)b
@endsection