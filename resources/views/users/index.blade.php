@extends('layout')

@section('title', 'Пользователи')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1>👥 Пользователи</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="/users/create" class="btn btn-success">+ Новый пользователь</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Создано</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><small class="text-muted">#{{ $user->id }}</small></td>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td><small class="text-muted">{{ $user->created_at?->format('d.m.Y H:i') }}</small></td>
                        <td>
                            <a href="/users/{{ $user->id }}" class="btn btn-sm btn-info">Просмотр</a>
                            <a href="/users/{{ $user->id }}/edit" class="btn btn-sm btn-warning">Редактировать</a>
                            <form action="/users/{{ $user->id }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Нет пользователей. <a href="/users/create">Создать первого</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
