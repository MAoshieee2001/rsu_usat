{!! Form::open(['route' => 'admin.vacations.store',  'method' => 'POST']) !!}
@include('admin.vacations.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}