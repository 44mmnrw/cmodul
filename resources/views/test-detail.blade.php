@extends('layout')

@section('title', 'Test Detail')

@section('content')
<div>
    <h1>{{ $detail->name }}</h1>
    <p>Найдено мест: {{ $detail->places->count() }}</p>
    
    @forelse($detail->places as $place)
        <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
            <strong>{{ $place->name }}</strong> ({{ $place->place_id }})
            <br>
            Количество: {{ $place->pivot->quantity }}
        </div>
    @empty
        <p>Нет мест</p>
    @endforelse
</div>
@endsection
