@extends('layout')

@section('title', 'Добавить деталь')

@section('content')
@include('details._form', [
    'detail' => null,
    'action' => route('details.store'),
    'title' => 'Добавить деталь',
    'backRoute' => route('details.index'),
    'categories' => $categories,
    'sources' => $sources
])
@endsection
