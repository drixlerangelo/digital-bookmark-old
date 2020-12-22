@extends('skeleton')

@section('page.title', 'Hello there, ' . Auth::user()->username . '!')

@section('page.content')
    <home-page username="{{ Auth::user()->username }}"></home-page>
@endsection

@push('page.script')
    <script type="text/javascript" src="{{ asset('js/home.js') }}"></script>
@endpush
