@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/calendars/index.css') }}">
@endsection

@section('title', 'お手伝いカレンダー')

@section('content')
  <livewire:calendar />
@endsection