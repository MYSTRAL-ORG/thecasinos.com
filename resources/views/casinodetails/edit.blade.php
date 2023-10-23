@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($casinodetail, array('route' => array('casinodetails.update', $casinodetail->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('title', 'Title', ['class'=>'form-label']) }}
			{{ Form::textarea('title', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('description', 'Description', ['class'=>'form-label']) }}
			{{ Form::textarea('description', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('sumup', 'Sumup', ['class'=>'form-label']) }}
			{{ Form::textarea('sumup', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('games', 'Games', ['class'=>'form-label']) }}
			{{ Form::textarea('games', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('fun_facts', 'Fun_facts', ['class'=>'form-label']) }}
			{{ Form::textarea('fun_facts', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('resume_1_line', 'Resume_1_line', ['class'=>'form-label']) }}
			{{ Form::text('resume_1_line', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('resume_2_words', 'Resume_2_words', ['class'=>'form-label']) }}
			{{ Form::textarea('resume_2_words', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
