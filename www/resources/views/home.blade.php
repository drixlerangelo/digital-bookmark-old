@extends('skeleton')

@section('page.title', 'Hello there, ' . Auth::user()->username . '!')

@section('page.content')
    <home-page></home-page>
@endsection

@push('page.style')
    {{--  TODO: add the homepage's style --}}
@endpush

@push('page.script')
    <script type="text/javascript" src="{{ asset('js/home.js') }}"></script>
@endpush
