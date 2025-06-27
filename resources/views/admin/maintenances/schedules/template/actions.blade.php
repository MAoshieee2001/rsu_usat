<div class="btn-group" role="group">
    <button class="btn btn-sm btn-warning btnEditar" id="{{ $row->id }}"><i class="fas fa-edit"></i></button>

    {!! Form::open([
        'route' => ['admin.maintenances.schedules.destroy', $maintenance->id, $row->id],
        'method' => 'DELETE',
        'class' => 'frmDelete d-inline'
    ]) !!}
        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
    {!! Form::close() !!}
</div>
