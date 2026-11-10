<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'conexion.php';

// Obtener listas desplegables para el formulario
$pacientes = $conexion->query("SELECT id, cedula, nombre, apellido FROM pacientes ORDER BY apellido ASC")->fetchAll();
$procedimientos = $conexion->query("SELECT id, nombre, costo FROM procedimientos ORDER BY nombre ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historias Clínicas - Clínica Odontológica</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="procedimientos.php"><i class="fa fa-medkit"></i> Procedimientos</a>
                </li>
                <li class="nav-item active">
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
            <h2 class="font-weight-bold text-dark"><i class="fa fa-file-text-o" aria-hidden="true"></i> Gestión de Historias Clínicas</h2>
            <hr class="w-25">
        </div>
    </div>

    <!-- Contenedor para Alertas Dinámicas -->
    <div class="row">
        <div class="col-12" id="result"></div>
    </div>

    <div class="row">
        <!-- Tabla de Listado (Columna Izquierda - Ancho 8) -->
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-list" aria-hidden="true"></i> Historial de Atenciones</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Paciente</th>
                                    <th scope="col">Procedimiento</th>
                                    <th scope="col" class="text-center">PDF</th>
                                </tr>
                            </thead>
                                <tbody id="listado">
                                <!-- Carga dinámica con AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Registro (Columna Derecha - Ancho 4) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fa fa-plus-circle" aria-hidden="true"></i> Nueva Atención Médica</h5>
                </div>
                <div class="card-body">
                    <form id="form">
                        <div class="form-group">
                            <label for="paciente_id"><i class="fa fa-user"></i> Seleccionar Paciente</label>
                            <select id="paciente_id" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($pacientes as $pat): ?>
                                    <option value="<?php echo $pat['id']; ?>">
                                        <?php echo htmlspecialchars($pat['cedula'] . ' - ' . $pat['nombre'] . ' ' . $pat['apellido']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="procedimiento_id"><i class="fa fa-medkit"></i> Procedimiento Realizado</label>
                            <select id="procedimiento_id" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($procedimientos as $proc): ?>
                                    <option value="<?php echo $proc['id']; ?>">
                                        <?php echo htmlspecialchars($proc['nombre'] . ' ($' . number_format($proc['costo'], 2) . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="diagnostico"><i class="fa fa-stethoscope"></i> Diagnóstico</label>
                            <textarea id="diagnostico" class="form-control" rows="3" placeholder="Descripción del diagnóstico..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="observaciones"><i class="fa fa-comment"></i> Observaciones / Notas</label>
                            <textarea id="observaciones" class="form-control" rows="2" placeholder="Indicaciones médicas..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-floppy-o" aria-hidden="true"></i> Registrar Atención</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Historial -->
<script>
$(document).ready(function() {
    listarHistorial();

    function listarHistorial() {
        $.ajax({
            url: 'ajax/historial_ajax.php',
            type: 'POST',
            data: { accion: 'listar' },
            success: function(response) {
                $('#listado').html(response);
            },
            error: function() {
                $('#result').html('<div class="alert alert-danger">Error al cargar el historial clínico.</div>');
            }
        });
    }

    $('#form').on('submit', function(e) {
        e.preventDefault();

        var paciente_id = $('#paciente_id').val();
        var procedimiento_id = $('#procedimiento_id').val();
        var diagnostico = $('#diagnostico').val();
        var observaciones = $('#observaciones').val();

        $.ajax({
            url: 'ajax/historial_ajax.php',
            type: 'POST',
            data: {
                accion: 'guardar',
                paciente_id: paciente_id,
                procedimiento_id: procedimiento_id,
                diagnostico: diagnostico,
                observaciones: observaciones
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#result').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    $('#form')[0].reset();
                    listarHistorial();
                } else {
                    $('#result').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            },
            error: function() {
                $('#result').html('<div class="alert alert-danger">Error de comunicación con el servidor.</div>');
            }
        });
    });
});
</script>

<!-- Bootstrap JS Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>