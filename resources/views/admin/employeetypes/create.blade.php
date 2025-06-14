{!! Form::open(['route' => 'admin.employeetypes.store', 'method' => 'POST']) !!}
@include('admin.employeetypes.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}