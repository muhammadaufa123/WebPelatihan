@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Course Materials</h1>
        <ul>
            @foreach($files as $file)
                <li><a href="{{ Storage::url($file) }}" download>{{ basename($file) }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection
