{!! Form::open(['route' => 'admin.types.store', 'method' => 'POST']) !!}
@include('admin.types.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}