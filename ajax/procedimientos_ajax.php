<?php
session_start();
require_once '../conexion.php';

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar':
        try {
            $stmt = $conexion->query("SELECT * FROM procedimientos ORDER BY id DESC");
            $procedimientos = $stmt->fetchAll();
            $html = '';
            $i = 1;
            
            if (count($procedimientos) > 0) {
                foreach ($procedimientos as $p) {
                    $html .= '<tr>';
                    $html .= '<th scope="row">' . $i++ . '</th>';
                    $html .= '<td>' . htmlspecialchars($p['nombre']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($p['descripcion']) . '</td>';
                    $html .= '<td>$' . number_format($p['costo'], 2) . '</td>';
                    $html .= '<td class="text-center">
                                <button class="btn btn-warning btn-sm btn-editar" data-id="' . $p['id'] . '" title="Editar"><i class="fa fa-pencil"></i></button>
                            </td>';
                    $html .= '<td class="text-center">
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $p['id'] . '" title="Eliminar"><i class="fa fa-trash"></i></button>
                            </td>';
                    $html .= '</tr>';
                }
            } else {
                $html = '<tr><td colspan="6" class="text-center text-muted">No hay procedimientos registrados.</td></tr>';
            }
            echo $html;
        } catch (PDOException $e) {
            echo '<tr><td colspan="6" class="text-center text-danger">Error al cargar datos.</td></tr>';
        }
        break;

    case 'guardar':
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $costo = trim($_POST['costo'] ?? '');

        if (empty($nombre) || empty($costo)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre y el costo son obligatorios.']);
            exit();
        }

        try {
            if (empty($id)) {
                // Insertar nuevo procedimiento
                $stmt = $conexion->prepare("INSERT INTO procedimientos (nombre, descripcion, costo) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $costo]);
                echo json_encode(['status' => 'success', 'message' => 'Procedimiento registrado exitosamente.']);
            } else {
                // Actualizar procedimiento existente
                $stmt = $conexion->prepare("UPDATE procedimientos SET nombre = ?, descripcion = ?, costo = ? WHERE id = ?");
                $stmt->execute([$nombre, $descripcion, $costo, $id]);
                echo json_encode(['status' => 'success', 'message' => 'Procedimiento actualizado exitosamente.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        break;

    case 'obtener':
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $conexion->prepare("SELECT * FROM procedimientos WHERE id = ?");
            $stmt->execute([$id]);
            $procedimiento = $stmt->fetch();
            
            if ($procedimiento) {
                echo json_encode(['status' => 'success', 'data' => $procedimiento]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Procedimiento no encontrado.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al consultar.']);
        }
        break;

    case 'eliminar':
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $conexion->prepare("DELETE FROM procedimientos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Procedimiento eliminado exitosamente.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar el procedimiento porque está asociado a historias clínicas existentes.']);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        break;
}
?>