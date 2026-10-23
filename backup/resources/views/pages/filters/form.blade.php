@extends(Template::master())

@section('title')
<h4>Master Filter</h4>
@endsection

@section('action')
<div class="button">
	<button type="submit" class="btn btn-primary" id="modal-btn-save">{{ __('Save') }}</button>
</div>
@endsection

@section('container')

{!! Template::form_open($model) !!}

@if(!request()->ajax())
<div class="page-header">
	<div class="header-container container-fluid d-sm-flex justify-content-between">
		@yield('title')
		@yield('action')
	</div>
</div>
@endif

<div class="card">
	<div class="card-body">

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Code</label>
					{!! Form::text('filter_code', null, ['class' => 'form-control', 'id' => 'filter_code', 'placeholder'
					=>
					'Please fill this input', 'required']) !!}
				</div>
				<div class="form-group">
					<label>Table</label>
					{!! Form::text('filter_table', null, ['class' => 'form-control', 'id' => 'filter_table',
					'placeholder' =>
					'Please fill this input', 'required']) !!}
				</div>
				<div class="form-group">
					<label>Function</label>
					{!! Form::text('filter_function', null, ['class' => 'form-control', 'id' => 'filter_function',
					'placeholder'
					=>
					'Please fill this input', 'required']) !!}
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label>Data From User</label>
					{!! Form::select('filter_from_user', $status, null, ['class' => 'form-control', 'id' =>
					'filter_from_user', 'placeholder' => '- Pilih Filter -', 'required']) !!}
				</div>

				<div class="form-group">
					<label>Field</label>
					{!! Form::text('filter_field', null, ['class' => 'form-control', 'id' => 'filter_field',
					'placeholder' => 'Please fill this input', 'required']) !!}
				</div>

				<div class="form-group">
					<label>Operator</label>
					<div class="row">
						<div class="col-md-6">
							{!! Form::text('filter_operator', null, ['class' => 'form-control', 'id' =>
							'filter_operator',
							'placeholder' => 'Please fill this input', 'required']) !!}
						</div>
						<div class="col-md-6">
							{!! Form::text('filter_value', null, ['class' => 'form-control', 'id' => 'filter_value',
							'placeholder' => 'Please fill this input', 'required']) !!}
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

{!! Template::form_close() !!}

@endsection

@push('javascript')
@include(Template::components('form'))
@endpush