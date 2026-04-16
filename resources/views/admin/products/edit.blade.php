@extends('admin.layout')

@section('title', 'Редактирование товара')
@section('page_title', 'Редактирование товара')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}">
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>
@endsection
