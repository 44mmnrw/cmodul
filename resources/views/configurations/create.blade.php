@extends('layout')


@section('content')
@include('configurations._form', [
    'cabinet' => null,
    'action' => route('cabinet.store'),
    'backRoute' => route('configurations.index', ['type' => $productType]),
    'currentComponents' => [],
    'availableComponents' => $availableComponents,
    'productType' => $productType,
    'productTypes' => $productTypes
])
@endsection
