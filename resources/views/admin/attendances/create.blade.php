{!! Form::open(['route' => 'admin.attendances.store',  'method' => 'POST']) !!}
@include('admin.attendances.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}