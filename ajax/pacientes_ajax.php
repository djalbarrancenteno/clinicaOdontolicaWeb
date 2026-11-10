<?php
session_start();
require_once '../conexion.php';

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar':
        try {
            $stmt = $conexion->query("SELECT * FROM pacientes ORDER BY id DESC");
            $pacientes = $stmt->fetchAll();
            $html = '';
            $i = 1;
            
            if (count($pacientes) > 0) {
                foreach ($pacientes as $p) {
                    $html .= '<tr>';
                    $html .= '<th scope="row">' . $i++ . '</th>';
                    $html .= '<td>' . htmlspecialchars($p['cedula']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($p['telefono']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($p['email']) . '</td>';
                    $html .= '<td class="text-center">
                                <button class="btn btn-warning btn-sm btn-editar" data-id="' . $p['id'] . '" title="Editar"><i class="fa fa-pencil"></i></button>
                            </td>';
                    $html .= '<td class="text-center">
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $p['id'] . '" title="Eliminar"><i class="fa fa-trash"></i></button>
                            </td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="7" class="text-center text-muted">No hay pacientes registrados.</td></tr>';
            }
            echo $html;
        } catch (PDOException $e) {
            echo '<tr><td colspan="7" class="text-center text-danger">Error al cargar datos.</td></tr>';
        }
        break;

    case 'guardar':
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');

        if (empty($cedula) || empty($nombre) || empty($apellido) || empty($telefono)) {
            echo json_encode(['status' => 'error', 'message' => 'Cédula, nombre, apellido y teléfono son obligatorios.']);
            exit();
        }

        try {
            if (empty($id)) {
                // Verificar si la cédula ya existe
                $stmtCheck = $conexion->prepare("SELECT id FROM pacientes WHERE cedula = ?");
                $stmtCheck->execute([$cedula]);
                if ($stmtCheck->fetch()) {
                    echo json_encode(['status' => 'error', 'message' => 'Ya existe un paciente registrado con esta cédula.']);
                    exit();
                }

                // Insertar nuevo paciente
                $stmt = $conexion->prepare("INSERT INTO pacientes (cedula, nombre, apellido, telefono, email, direccion, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$cedula, $nombre, $apellido, $telefono, $email, $direccion, $fecha_nacimiento ?: null]);
                echo json_encode(['status' => 'success', 'message' => 'Paciente registrado exitosamente.']);
            } else {
                // Actualizar paciente existente
                $stmt = $conexion->prepare("UPDATE pacientes SET cedula = ?, nombre = ?, apellido = ?, telefono = ?, email = ?, direccion = ?, fecha_nacimiento = ? WHERE id = ?");
                $stmt->execute([$cedula, $nombre, $apellido, $telefono, $email, $direccion, $fecha_nacimiento ?: null, $id]);
                echo json_encode(['status' => 'success', 'message' => 'Paciente actualizado exitosamente.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        break;

    case 'obtener':
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE id = ?");
            $stmt->execute([$id]);
            $paciente = $stmt->fetch();
            
            if ($paciente) {
                echo json_encode(['status' => 'success', 'data' => $paciente]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Paciente no encontrado.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al consultar.']);
        }
        break;

    case 'eliminar':
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $conexion->prepare("DELETE FROM pacientes WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Paciente eliminado exitosamente.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar el paciente.']);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        break;
}
?>