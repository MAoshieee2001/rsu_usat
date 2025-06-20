{!! Form::open(['route' => 'admin.routes.store', 'files' => true, 'method' => 'POST',]) !!}
@include('admin.routes.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}