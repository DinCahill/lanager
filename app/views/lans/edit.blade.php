@extends('layouts.default')
@section('content')
	@include('layouts.default.title')
	@include('layouts.default.alerts')

	{{ Form::model($lan, ['route' => ['lans.update', $lan->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
		@include('lans.partials.form')
	{{ Form::close() }}

@endsection
