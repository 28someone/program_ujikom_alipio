@extends('layouts.app')

@section('title', 'Edit Kategori Kelas')

@section('content')
    @include('class-categories.form', ['action' => route('class-categories.update', $classCategory), 'method' => 'PUT'])
@endsection
