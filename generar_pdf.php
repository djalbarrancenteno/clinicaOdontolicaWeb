<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'conexion.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    die("ID de historia clínica no especificado.");
}

try {
    $stmt = $conexion->prepare("
        SELECT h.*, p.cedula, p.nombre AS p_nombre, p.apellido AS p_apellido, p.telefono, p.email, p.direccion, p.fecha_nacimiento,
            pr.nombre AS proc_nombre, pr.descripcion AS proc_desc, pr.costo,
            u.nombre AS u_nombre, u.apellido AS u_apellido
        FROM historial_clinico h
        JOIN pacientes p ON h.paciente_id = p.id
        JOIN procedimientos pr ON h.procedimiento_id = pr.id
        JOIN usuarios u ON h.usuario_id = u.id
        WHERE h.id = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        die("Historia clínica no encontrada.");
    }
} catch (PDOException $e) {
    die("Error al consultar la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia Clínica - <?php echo htmlspecialchars($data['p_nombre'] . ' ' . $data['p_apellido']); ?></title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { background: #fff !important; color: #000; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        @media print {
            .no-print { display: none !important; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body onload="window.print();">

<div class="container my-5">
    <div class="text-center mb-4 no-print">
        <button onclick="window.print();" class="btn btn-primary btn-lg"><i class="fa fa-print"></i> Guardar como PDF / Imprimir</button>
        <a href="historial.php" class="btn btn-secondary btn-lg"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>

    <div class="invoice-box">
        <div class="row mb-4">
            <div class="col-6">
                <h3><i class="fa fa-stethoscope text-primary"></i> Clínica Odontológica</h3>
                <p class="text-muted mb-0">Sistema de Gestión e Historias Clínicas</p>
            </div>
            <div class="col-6 text-right">
                <h5 class="font-weight-bold">HISTORIA CLÍNICA</h5>
                <p class="mb-0"><strong>Nº de Registro:</strong> #<?php echo str_pad($data['id'], 5, '0', STR_PAD_LEFT); ?></p>
                <p class="mb-0"><strong>Fecha:</strong> <?php echo htmlspecialchars($data['fecha_atencion']); ?></p>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="bg-primary text-white p-2 rounded"><i class="fa fa-user"></i> Datos del Paciente</h5>
                <table class="table table-bordered table-sm mb-0">
                    <tr>
                        <th style="width: 25%;">Cédula:</th>
                        <td><?php echo htmlspecialchars($data['cedula']); ?></td>
                        <th style="width: 25%;">Teléfono:</th>
                        <td><?php echo htmlspecialchars($data['telefono']); ?></td>
                    </tr>
                    <tr>
                        <th>Nombres y Apellidos:</th>
                        <td colspan="3"><?php echo htmlspecialchars($data['p_nombre'] . ' ' . $data['p_apellido']); ?></td>
                    </tr>
                    <tr>
                        <th>Correo Electrónico:</th>
                        <td><?php echo htmlspecialchars($data['email'] ?: 'No registrado'); ?></td>
                        <th>Fecha de Nacimiento:</th>
                        <td><?php echo htmlspecialchars($data['fecha_nacimiento'] ?: 'No registrada'); ?></td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td colspan="3"><?php echo htmlspecialchars($data['direccion'] ?: 'No registrada'); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="bg-secondary text-white p-2 rounded"><i class="fa fa-medkit"></i> Detalle del Procedimiento</h5>
                <table class="table table-bordered table-sm mb-0">
                    <tr>
                        <th style="width: 25%;">Procedimiento:</th>
                        <td><?php echo htmlspecialchars($data['proc_nombre']); ?></td>
                        <th style="width: 25%;">Costo:</th>
                        <td>$<?php echo number_format($data['costo'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Descripción del Servicio:</th>
                        <td colspan="3"><?php echo htmlspecialchars($data['proc_desc']); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="bg-dark text-white p-2 rounded"><i class="fa fa-file-text"></i> Diagnóstico y Observaciones Médicas</h5>
                <div class="p-3 border rounded bg-light">
                    <p><strong>Diagnóstico:</strong></p>
                    <p class="text-justify"><?php echo nl2br(htmlspecialchars($data['diagnostico'])); ?></p>
                    <hr>
                    <p><strong>Observaciones / Indicaciones:</strong></p>
                    <p class="text-justify"><?php echo nl2br(htmlspecialchars($data['observaciones'] ?: 'Sin observaciones adicionales.')); ?></p>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4">
            <div class="col-6 text-center">
                <p>______________________________________</p>
                <p class="mb-0"><strong>Dr(a). <?php echo htmlspecialchars($data['u_nombre'] . ' ' . $data['u_apellido']); ?></strong></p>
                <p class="text-muted small">Odontólogo Tratante</p>
            </div>
            <div class="col-6 text-center">
                <p>______________________________________</p>
                <p class="mb-0"><strong>Firma del Paciente</strong></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>