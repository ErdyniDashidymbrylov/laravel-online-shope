@extends('admin.layout')

@section('title', 'Новый товар')
@section('page_title', 'Добавление товара')

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        @include('admin.products._form')
    </form>
@endsection
