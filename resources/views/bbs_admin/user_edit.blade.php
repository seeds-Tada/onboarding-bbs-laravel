@extends('layouts.bbs_admin')

@section('title', 'ユーザー編集画面')

@section('content')
<form action="{{ url('/admin/users/'.$role.'_edit_complete/'.$id) }}" method="post">
	@csrf
	<input name="id" id="id" value="{{$id}}" readonly hidden>
	<table class="post-users-table">
		<tbody>
			<tr>
				<th>ユーザーネーム</th>
				<td>
					<input type="text" name="name" id="name" value="{{ old('name', $data->name) }}" required>
				</td>
			</tr>
			<tr>
				<th>メールアドレス</th>
				<td>
					<input type="email" name="email" id="email" value="{{ old('email', $data->email) }}" required>
				</td>
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