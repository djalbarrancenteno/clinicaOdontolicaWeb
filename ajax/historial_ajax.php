<?php
session_start();
require_once '../conexion.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        try {
            $sql = "SELECT h.id, h.fecha_atencion, p.cedula, p.nombre AS p_nombre, p.apellido AS p_apellido, pr.nombre AS proc_nombre 
                    FROM historial_clinico h
                    JOIN pacientes p ON h.paciente_id = p.id
                    JOIN procedimientos pr ON h.procedimiento_id = pr.id
                    ORDER BY h.id DESC";
            $stmt = $conexion->query($sql);
            $historiales = $stmt->fetchAll();
            $html = '';
            $i = 1;
            
            if (count($historiales) > 0) {
                foreach ($historiales as $h) {
                    $html .= '<tr>';
                    $html .= '<th scope="row">' . $i++ . '</th>';
                    $html .= '<td>' . htmlspecialchars($h['fecha_atencion']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($h['cedula'] . ' - ' . $h['p_nombre'] . ' ' . $h['p_apellido']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($h['proc_nombre']) . '</td>';
                    $html .= '<td class="text-center">
                                <a href="generar_pdf.php?id=' . $h['id'] . '" target="_blank" class="btn btn-danger btn-sm" title="Generar PDF"><i class="fa fa-file-pdf-o"></i> PDF</a>
                            </td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="5" class="text-center text-muted">No hay atenciones registradas.</td></tr>';
            }
            echo $html;
        } catch (PDOException $e) {
            echo '<tr><td colspan="5" class="text-center text-danger">Error al cargar datos.</td></tr>';
        }
        break;

    case 'guardar':
        header('Content-Type: application/json');
        $paciente_id = $_POST['paciente_id'] ?? '';
        $procedimiento_id = $_POST['procedimiento_id'] ?? '';
        $diagnostico = trim($_POST['diagnostico'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');
        $usuario_id = $_SESSION['usuario_id'];

        if (empty($paciente_id) || empty($procedimiento_id) || empty($diagnostico)) {
            echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar el paciente, el procedimiento y escribir el diagnóstico.']);
            exit();
        }

        try {
            $stmt = $conexion->prepare("INSERT INTO historial_clinico (paciente_id, procedimiento_id, usuario_id, diagnostico, observaciones) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$paciente_id, $procedimiento_id, $usuario_id, $diagnostico, $observaciones]);
            echo json_encode(['status' => 'success', 'message' => 'Historia clínica registrada exitosamente.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        break;
}
?>