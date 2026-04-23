@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
    @include('books.form', ['action' => route('books.update', $book), 'method' => 'PUT'])
@endsection
