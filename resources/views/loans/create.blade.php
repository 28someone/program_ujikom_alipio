@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    @include('loans.form', ['action' => route('loans.store'), 'method' => 'POST'])
@endsection
