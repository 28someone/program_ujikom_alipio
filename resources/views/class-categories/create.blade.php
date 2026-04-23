@extends('layouts.app')

@section('title', 'Tambah Kategori Kelas')

@section('content')
    @include('class-categories.form', ['action' => route('class-categories.store'), 'method' => 'POST'])
@endsection
