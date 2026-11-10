<?php
session_start();
// Si ya hay una sesión activa, redirigir directamente al panel principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Clínica Odontológica</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fa fa-stethoscope"></i> Clínica Odontológica</h4>
                </div>
                <div class="card-body">
                    <!-- Contenedor para alertas dinámicas de AJAX -->
                    <div id="resultado_login"></div>

                    <form id="formLogin">
                        <div class="form-group">
                            <label for="email"><i class="fa fa-envelope"></i> Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="admin@odontologia.com" required>
                        </div>
                        <div class="form-group">
                            <label for="clave"><i class="fa fa-lock"></i> Contraseña</label>
                            <input type="password" id="clave" name="clave" class="form-control" placeholder="Contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-sign-in" aria-hidden="true"></i> Ingresar
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>Acceso exclusivo para personal autorizado</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Login -->
<script>
$(document).ready(function() {
    $('#formLogin').on('submit', function(e) {
        e.preventDefault(); // Evita la recarga tradicional del formulario

        var email = $('#email').val();
        var clave = $('#clave').val();

        $.ajax({
            url: 'ajax/login_ajax.php',
            type: 'POST',
            data: { email: email, clave: clave },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#resultado_login').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    $('#resultado_login').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#resultado_login').html('<div class="alert alert-danger">Error de comunicación con el servidor.</div>');
            }
        });
    });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>