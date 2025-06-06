{!! Form::open(['route' => 'admin.contracts.store', 'files' => true, 'method' => 'POST']) !!}
@include('admin.contracts.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}