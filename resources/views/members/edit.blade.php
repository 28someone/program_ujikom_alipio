@extends('layouts.app')

@section('title', 'Edit Anggota')

@section('content')
    @include('members.form', ['action' => route('members.update', $member), 'method' => 'PUT'])
@endsection
