{!! Form::open(['route' => 'admin.routescoords.store', 'files' => true, 'method' => 'POST',]) !!}
@include('admin.routescoords.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}