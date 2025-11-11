@extends('layouts.bbs_admin')

@section('title', '管理者ユーザー管理画面')

@section('content')
<br>
@if( session("flash") )
	@foreach( session("flash") as $key => $item )
		<div class="flash-alert flash-alert-{{ $key }}">
			{{ session("flash.".$key) }}
		</div>
	@endforeach
@endif
<div class="users-area">
	<div class="admin-users users">
		<div class="post-users-table-area">
			<form action="{{ url('/admin/users/admin_create') }}" method="post">
				@csrf
				<table class="post-users-table">
					<thead>
						<tr>
							<th colspan="2">管理者ユーザー新規作成</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><label for="admin_name">名前</label></th>
							<td><input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name') }}" required></td>
						</tr>
						<tr>
							<th><label for="admin_email">メールアドレス</label></th>
							<td><input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" required></td>
						</tr>
						<tr>
							<th><label for="password">パスワード</label></th>
							<td><input type="password" name="password" id="password" required></td>
						</tr>
					</tbody>
				</table>
				<button type="submit">投稿</button>
			</form>
		</div>
		<div class="users-table-area">
			<table class="users-table">
				<thead>
					<tr>
						<th>ユーザーid</th>
						<th>ユーザーネーム</th>
						<th>メールアドレス</th>
						<th>アカウント作成日</th>
						<th>アカウント最終更新日</th>
						<th>編集</th>
						<th>削除</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($adminsData as $admin)
						<tr>
							<td>{{ $admin['id'] }}</td>
							<td>{{ $admin['name'] }}</td>
							<td>{{ $admin['email'] }}</td>
							<td>{{ $admin['created_at'] }}</td>
							<td>{{ $admin['updated_at'] }}</td>
							<td>
								<form action="{{ url('/admin/users/admin_edit/'.$admin['id']) }}" method="post" id="admin-edit-{{ $admin['id'] }}">
									@csrf
									<input type="hidden" name="id" value="{{ $admin['id'] }}">
									<button type="submit">編集</button>
								</form>
							</td>
							<td>
								<form action="{{ url('/admin/users/admin_delete_confirm/'.$admin['id']) }}" method="post" id="admin-delete-{{ $admin['id'] }}">
									@csrf
									<input type="hidden" name="id" value="{{ $admin['id'] }}">
									<button type="submit">削除</button>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<br>
	<div class="general-users users">
		<div class="post-users-table-area">
			<form action="{{ url('/admin/users/user_create') }}" method="post">
				@csrf
				<table class="post-users-table">
					<thead>
						<tr>
							<th colspan="2">一般ユーザー新規作成</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><label for="name">名前</label></th>
							<td><input type="text" name="name" id="name" value="{{ old('name') }}" required></td>
						</tr>
						<tr>
							<th><label for="email">メールアドレス</label></th>
							<td><input type="email" name="email" id="email" value="{{ old('email') }}" required></td>
						</tr>
						<tr>
							<th><label for="password">パスワード</label></th>
							<td><input type="password" name="password" id="password" required></td>
						</tr>
					</tbody>
				</table>
				<button type="submit">投稿</button>
			</form>
		</div>
		<div class="users-table-area">
			<table class="users-table">
				<thead>
					<tr>
						<th>ユーザーid</th>
						<th>ユーザーネーム</th>
						<th>メールアドレス</th>
						<th>アカウント作成日</th>
						<th>アカウント最終更新日</th>
						<th>編集</th>
						<th>削除</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($usersData as $user)
						<tr>
							<td>{{ $user['id'] }}</td>
							<td>{{ $user['name'] }}</td>
							<td>{{ $user['email'] }}</td>
							<td>{{ $user['created_at'] }}</td>
							<td>{{ $user['updated_at'] }}</td>
							<td>
								<form action="{{ url('/admin/users/user_edit/'.$user['id']) }}" method="post" id="user-edit-{{ $user['id'] }}">
									@csrf
									<input type="hidden" name="id" value="{{ $user['id'] }}">
									<button type="submit">編集</button>
								</form>
							</td>
							<td>
								<form action="{{ url('/admin/users/user_delete_confirm/'.$user['id']) }}" method="post" id="user-delete-{{ $user['id'] }}">
									@csrf
									<input type="hidden" name="id" value="{{ $user['id'] }}">
									<button type="submit">削除</button>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@if (count($errors) > 0)
	{{ var_dump($errors) }}
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