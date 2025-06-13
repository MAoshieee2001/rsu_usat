{!! Form::open(['route' => 'admin.zonescoords.store', 'files' => true, 'method' => 'POST',]) !!}
@include('admin.zonescoords.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}