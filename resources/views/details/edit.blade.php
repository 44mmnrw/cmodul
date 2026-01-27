@extends('layout')

@section('title', 'Редактировать ' . $detail->name)

@section('content')
@include('details._form', [
    'detail' => $detail,
    'action' => route('details.update', $detail),
    'title' => 'Редактировать',
    'backRoute' => route('details.show', $detail),
    'categories' => $categories,
    'sources' => $sources
])
@endsection
