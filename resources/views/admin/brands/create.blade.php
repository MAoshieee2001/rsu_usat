{!! Form::open(['route' => 'admin.brands.store', 'files' => true, 'method' => 'POST']) !!}
@include('admin.brands.template.form')
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>

{!! Form::close() !!}