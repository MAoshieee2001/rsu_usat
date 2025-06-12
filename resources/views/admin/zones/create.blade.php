{!! Form::open(['route' => 'admin.zones.store', 'files' => true, 'method' => 'POST',]) !!}
@include('admin.zones.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}