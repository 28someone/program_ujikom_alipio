@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('content')
    @include('members.form', ['action' => route('members.store'), 'method' => 'POST'])
@endsection
