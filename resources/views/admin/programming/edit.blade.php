{!! Form::model($programacion, ['route' => ['admin.programming.update', $programacion->id], 'method' => 'PUT']) !!}
    @include('admin.programming.template.form')
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Actualizar
    </button>
{!! Form::close() !!}
