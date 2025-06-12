@extends('adminlte::page')

@section('title', 'Colores')

@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search"></i> Listado de Colores</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center" id="tbtEntity">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Creación</th>
                        <th>Actualización</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Se carga dinámicamente por AJAX --}}
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-plus"></i> Nuevo Registro</button>
        <a href="{{ route('admin.colors.index') }}" class="btn btn-success"><i class="fas fa-sync"></i> Actualizar</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">...</div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#tbtEntity').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '/js/es-ES.json'
                },
                "ajax": "{{ route('admin.colors.index') }}",
                "columns": [
                    {
                        "data": "name",
                        "width": "15%"
                    },
                    {
                        "data": "code",
                        "render": function (data) {
                            return '<span class="badge" style="background-color:' + data + '; color: #fff;">' + data + '</span>';
                        },
                        "width": "10%"
                    },
                    {
                        "data": "created_at",
                        "width": "10%"
                    },
                    {
                        "data": "updated_at",
                        "width": "10%"
                    },
                    {
                        "data": "options",
                        "orderable": false,
                        "searchable": false,
                        "width": "4%"
                    },
                ]
            });
        });

        $('#btnNuevo').click(function () {
            $.ajax({
                url: "{{ route('admin.colors.create') }}",
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-plus'></i> Nuevo Color");
                    $('#ModalCenter .modal-body').html(response);
                    $('#ModalCenter').modal('show');

                    $('#ModalCenter form').on('submit', function (e) {
                        e.preventDefault();
                        var form = $(this);
                        var formdata = new FormData(this);
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formdata,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                $('#ModalCenter').modal('hide');
                                refreshTable();
                                Swal.fire({
                                    title: "Proceso exitoso",
                                    icon: "success",
                                    text: response.message,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: xhr.responseJSON.message,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    });
                }
            });
        });

        $(document).on('click', '.btnEditar', function () {
            var id = $(this).attr("id");
            $.ajax({
                url: "{{ route('admin.colors.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-edit'></i> Editar Color");
                    $('#ModalCenter .modal-body').html(response);
                    $('#ModalCenter').modal('show');

                    $('#ModalCenter form').on('submit', function (e) {
                        e.preventDefault();
                        var form = $(this);
                        var formdata = new FormData(this);
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formdata,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                $('#ModalCenter').modal('hide');
                                refreshTable();
                                Swal.fire({
                                    title: "Proceso exitoso",
                                    icon: "success",
                                    text: response.message,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: xhr.responseJSON.message,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    });
                }
            });
        });

        $(document).on('submit', '.frmDelete', function (e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "¿Está seguro de eliminar?",
                text: "Este proceso no es reversible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function (response) {
                            refreshTable();
                            Swal.fire({
                                title: "Proceso exitoso",
                                icon: "success",
                                text: response.message,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: xhr.responseJSON.message,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }
                    });
                }
            });
        });

        function refreshTable() {
            $('#tbtEntity').DataTable().ajax.reload(null, false);
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "Proceso exitoso",
                icon: "success",
                text: "{{ session('success') }}",
                timer: 2000,
                timerProgressBar: true
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "{{ session('error') }}",
                timer: 2000,
                timerProgressBar: true
            });
        </script>
    @endif
@endsection

@section('css')
{{-- Estilos personalizados opcionales --}}
@stop