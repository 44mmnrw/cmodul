@extends('layout')

@section('content')
@include('configurations._form', [
    'cabinet' => $cabinet,
    'action' => route('configuration-update', $cabinet->id),
    'backRoute' => route('configuration-detail', $cabinet->id),
    'currentComponents' => $currentComponents,
    'availableComponents' => $availableComponents,
    'productTypes' => $productTypes
])
@endsection
