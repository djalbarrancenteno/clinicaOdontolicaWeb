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
    <title>Panel Principal - Clínica Odontológica</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Font Awesome -->
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
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php"><i class="fa fa-home"></i> Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pacientes.php"><i class="fa fa-users"></i> Pacientes</a>
                </li>
                <li class="nav-item">
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

<!-- Contenido Principal del Dashboard -->
<div class="container mt-4 mb-5 flex-grow-1">
    <div class="row mb-4">
        <div class="col-12">
            <div class="jumbotron bg-white shadow-sm border">
                <h1 class="display-5 text-dark"><i class="fa fa-user-md text-primary"></i> Bienvenid@, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
                <p class="lead text-muted">Panel de control principal para la administración de historias clínicas, pacientes y procedimientos odontológicos.</p>
                <hr class="my-4">
                <p>Utiliza el menú superior para navegar entre los diferentes módulos del sistema.</p>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Acceso Rápido -->
    <div class="row text-center">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <i class="fa fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="card-title font-weight-bold">Gestión de Pacientes</h5>
                        <p class="card-text text-muted">Registrar, actualizar y consultar información de los pacientes de la clínica.</p>
                    </div>
                    <a href="pacientes.php" class="btn btn-outline-primary btn-block mt-3">Ir a Pacientes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <i class="fa fa-medkit fa-3x text-success mb-3"></i>
                        <h5 class="card-title font-weight-bold">Procedimientos</h5>
                        <p class="card-text text-muted">Administrar el catálogo de servicios (limpiezas, calzas, extracciones, etc.).</p>
                    </div>
                    <a href="procedimientos.php" class="btn btn-outline-success btn-block mt-3">Ir a Procedimientos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <i class="fa fa-file-text-o fa-3x text-info mb-3"></i>
                        <h5 class="card-title font-weight-bold">Historias Clínicas</h5>
                        <p class="card-text text-muted">Llevar el historial de atenciones y generar documentos PDF.</p>
                    </div>
                    <a href="historial.php" class="btn btn-outline-info btn-block mt-3">Ir a Historias</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white border-top py-3 mt-auto text-center text-muted">
    <div class="container">
        <small><i class="fa fa-code"></i> Clínica Odontológica &copy; 2026 — Desarrollado con PHP, AJAX, Bootstrap y MySQL</small>
    </div>
</footer>

<!-- Bootstrap JS Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>