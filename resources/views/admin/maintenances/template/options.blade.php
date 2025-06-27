<button class="btn btn-sm btn-warning btnEditar" id="{{ $item->id }}">
    <i class="fas fa-edit"></i>
</button>
<form action="{{ route('admin.maintenances.destroy', $item->id) }}" method="POST" class="d-inline frmDelete">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        <i class="fas fa-trash"></i>
    </button>
</form>
