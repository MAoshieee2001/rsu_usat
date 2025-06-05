{!! Form::open(['route' => 'admin.colors.store', 'method' => 'POST']) !!}
    @include('admin.colors.template.form')
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
{!! Form::close() !!}
