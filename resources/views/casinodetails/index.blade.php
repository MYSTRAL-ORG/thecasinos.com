@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('casinodetails.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>title</th>
				<th>description</th>
				<th>sumup</th>
				<th>games</th>
				<th>fun_facts</th>
				<th>resume_1_line</th>
				<th>resume_2_words</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($casinodetails as $casinodetail)

				<tr>
					<td>{{ $casinodetail->id }}</td>
					<td>{{ $casinodetail->title }}</td>
					<td>{{ $casinodetail->description }}</td>
					<td>{{ $casinodetail->sumup }}</td>
					<td>{{ $casinodetail->games }}</td>
					<td>{{ $casinodetail->fun_facts }}</td>
					<td>{{ $casinodetail->resume_1_line }}</td>
					<td>{{ $casinodetail->resume_2_words }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('casinodetails.show', [$casinodetail->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('casinodetails.edit', [$casinodetail->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['casinodetails.destroy', $casinodetail->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
