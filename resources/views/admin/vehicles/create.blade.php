{!! Form::open(['route' => 'admin.vehicles.store', 'files' => true, 'method' => 'POST',]) !!}
@include('admin.vehicles.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}