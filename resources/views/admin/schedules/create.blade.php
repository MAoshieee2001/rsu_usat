{!! Form::open(['route' => 'admin.schedules.store', 'method' => 'POST']) !!}
@include('admin.schedules.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}