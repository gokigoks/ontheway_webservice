@extends('layouts.master')

@section('content')

<div class="form">
	<h3>rome 2 rio</h3>
	<form class="form-horizontal" role="form" method="POST" action="{{ url('/test/rome2rio') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="form-group">
			<label class="col-md-4 control-label">origin</label>
			<div class="col-md-6">
				<input type="text" class="form-control" name="origin" value="{{ old('origin') }}">
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label">destination</label>
			<div class="col-md-6">
				<input type="text" class="form-control" name="destination" value="{{ old('destination') }}">
			</div>
		</div>						

		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
				<button type="submit" class="btn btn-primary">query</button>

				
			</div>
		</div>
	</form>
</div>

@endsection