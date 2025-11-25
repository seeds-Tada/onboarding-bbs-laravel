@extends('layouts.bbs_admin')

@section('title', 'ユーザー削除確認')

@section('content')
<div>下記のユーザーを削除しますがよろしいですか?</div>
<table class="post-users-table">
	<tbody>
		<tr>
			<th>ユーザーネーム</th>
			<td>
				@if( $role === 'admin' )
					<span>{{ $data->admin_name }}</span>
				@else
					<span>{{ $data->name }}</span>
				@endif

			</td>
		</tr>
		</tr>
			<th>メールアドレス</th>
			<td>
				@if( $role === 'admin' )
					<span>{{ $data->dmin_email }}</span>
				@else
					<span>{{ $data->email }}</span>
				@endif
			</td>
		</tr>
	</tbody>
</table>
<form action="{{ url('/admin/users/'.$role.'_delete_complete/'.$id) }}" method="post">
	@csrf
	<input name="id" id="id" value="{{$id}}" readonly hidden>
	<button type="submit">削除</button>
</form>
@endsection

@section('footer')
(  ・ω・)ノ⌒■
@endsection