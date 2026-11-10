<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Procedimientos - Clínica Odontológica</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!-- Menú de Opciones / Barra de Navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php"><i class="fa fa-stethoscope"></i> Clínica Odontológica</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fa fa-home"></i> Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pacientes.php"><i class="fa fa-users"></i> Pacientes</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="procedimientos.php"><i class="fa fa-medkit"></i> Procedimientos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="historial.php"><i class="fa fa-file-text-o"></i> Historias Clínicas</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item text-danger" href="logout.php"><i class="fa fa-sign-out"></i> Cerrar Sesión</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5 flex-grow-1">
    <!-- Título Principal -->
    <div class="row justify-content-center mb-4">
        <div class="col text-center">
            <h2 class="font-weight-bold text-dark"><i class="fa fa-medkit" aria-hidden="true"></i> Gestión de Procedimientos Odontológicos</h2>
            <hr class="w-25">
        </div>
    </div>

    <!-- Contenedor para Alertas Dinámicas (Éxito / Error) -->
    <div class="row">
        <div class="col-12" id="result">
            <!-- Inyeccion de alertas mediante JavaScript -->
        </div>
    </div>

    <div class="row">
        <!-- Tabla de Listado (Columna Izquierda - Ancho 8) -->
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-list" aria-hidden="true"></i> Lista de Procedimientos Registrados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Costo ($)</th>
                                    <th scope="col" colspan="2" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                                <tbody id="listado">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Registro / Edición (Columna Derecha - Ancho 4) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fa fa-plus-square" aria-hidden="true"></i> Formulario de Procedimiento</h5>
                </div>
                <div class="card-body">
                    <form id="form">
                        <!-- Campo oculto para almacenar el ID al momento de editar -->
                        <input type="hidden" id="id">
                        
                        <div class="form-group">
                            <label for="nombre"><i class="fa fa-stethoscope"></i> Nombre del Procedimiento</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Ej. Limpieza Dental" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion"><i class="fa fa-file-text"></i> Descripción</label>
                            <textarea id="descripcion" class="form-control" rows="3" placeholder="Detalles del procedimiento..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="costo"><i class="fa fa-usd"></i> Costo ($)</label>
                            <input type="number" step="0.01" id="costo" class="form-control" placeholder="0.00" required>
                        </div>

                        <div class="d-flex">
                            <button type="submit" class="btn btn-success btn-block mr-1"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                            <button type="button" id="btnCancelar" class="btn btn-secondary btn-block mt-0" style="display:none;"><i class="fa fa-ban"></i> Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script AJAX y Lógica de Interfaz -->
<script>
$(document).ready(function() {
    // Cargar listado al iniciar la página
    listarProcedimientos();

    function listarProcedimientos() {
        $.ajax({
            url: 'ajax/procedimientos_ajax.php',
            type: 'POST',
            data: { accion: 'listar' },
            success: function(response) {
                $('#listado').html(response);
            },
            error: function() {
                $('#result').html('<div class="alert alert-danger">Error al cargar la lista de procedimientos.</div>');
            }
        });
    }

    // Guardar o Actualizar Registro
    $('#form').on('submit', function(e) {
        e.preventDefault();

        var id = $('#id').val();
        var nombre = $('#nombre').val();
        var descripcion = $('#descripcion').val();
        var costo = $('#costo').val();

        $.ajax({
            url: 'ajax/procedimientos_ajax.php',
            type: 'POST',
            data: {
                accion: 'guardar',
                id: id,
                nombre: nombre,
                descripcion: descripcion,
                costo: costo
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#result').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    limpiarFormulario();
                    listarProcedimientos();
                } else {
                    $('#result').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            },
            error: function() {
                $('#result').html('<div class="alert alert-danger">Error de comunicación con el servidor.</div>');
            }
        });
    });

    // Obtener datos para Editar
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');

        $.ajax({
            url: 'ajax/procedimientos_ajax.php',
            type: 'POST',
            data: { accion: 'obtener', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#id').val(response.data.id);
                    $('#nombre').val(response.data.nombre);
                    $('#descripcion').val(response.data.descripcion);
                    $('#costo').val(response.data.costo);
                    $('#btnCancelar').show();
                    $('html, body').animate({ scrollTop: 0 }, 'fast');
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Botón Cancelar Edición
    $('#btnCancelar').on('click', function() {
        limpiarFormulario();
    });

    function limpiarFormulario() {
        $('#form')[0].reset();
        $('#id').val('');
        $('#btnCancelar').hide();
    }

    // Eliminar Registro
    $(document).on('click', '.btn-eliminar', function() {
        if (confirm('¿Estás seguro de eliminar este procedimiento?')) {
            var id = $(this).data('id');

            $.ajax({
                url: 'ajax/procedimientos_ajax.php',
                type: 'POST',
                data: { accion: 'eliminar', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#result').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        listarProcedimientos();
                    } else {
                        $('#result').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }
                }
            });
        }
    });
});
</script>

<!-- Bootstrap JS Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>