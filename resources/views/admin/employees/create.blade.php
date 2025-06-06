{!! Form::open(['route' => 'admin.employees.store', 'files' => true, 'method' => 'POST',]) !!}
@include('admin.employees.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}