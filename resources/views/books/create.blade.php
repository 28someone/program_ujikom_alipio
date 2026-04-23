@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    @include('books.form', ['action' => route('books.store'), 'method' => 'POST'])
@endsection
