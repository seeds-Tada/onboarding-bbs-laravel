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
					@if( $role === 'admin' )
						<input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name', $data->admin_name) }}" required>
					@else
						<input type="text" name="name" id="name" value="{{ old('name', $data->name) }}" required>
					@endif
				</td>
			</tr>
			<tr>
				<th>メールアドレス</th>
				<td>
					@if( $role === 'admin' )
						<input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email', $data->admin_email) }}" required>
					@else
						<input type="email" name="email" id="email" value="{{ old('email', $data->email) }}" required>
					@endif
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