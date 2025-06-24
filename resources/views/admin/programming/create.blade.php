{{-- resources/views/admin/programming/create.blade.php --}}
{!! Form::open(['route' => 'admin.programming.store', 'method' => 'POST']) !!}
    @include('admin.programming.template.form')
    <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save"></i> Registrar
    </button>
{!! Form::close() !!}