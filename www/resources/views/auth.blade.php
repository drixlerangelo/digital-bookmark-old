@extends('skeleton')

@section('page.title', 'Welcome to Digital Bookmark!')

@section('page.content')
    <auth-page></auth-page>
@endsection

@push('page.script')
    <script type="text/javascript" src="{{ asset('js/auth.js') }}"></script>
@endpush
