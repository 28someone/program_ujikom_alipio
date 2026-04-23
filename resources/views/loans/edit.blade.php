@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    @include('loans.edit-form', ['action' => route('loans.update', $loan), 'method' => 'PUT'])
@endsection
