<?php
session_start();
include("../connection/db.php"); // Corregido: ruta correcta al archivo de conexión

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];
    $cantidad = max(1, intval($_POST['cantidad'] ?? 1));
    $accion = $_POST['accion'];

    // Verificar stock actual
    $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($stock_actual);
    $stmt->fetch();
    $stmt->close();

    if ($accion === 'agregar') {
        $cantidad_existente = $_SESSION['carrito'][$id]['cantidad'] ?? 0;
        $total_deseado = $cantidad_existente + $cantidad;

        if ($total_deseado > $stock_actual) {
            $_SESSION['mensaje_error'] = "❌ Solo quedan $stock_actual unidades en stock de '$nombre'.";
        } else {
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
            } else {
                $_SESSION['carrito'][$id] = [
                    'nombre' => $nombre,
                    'precio' => $precio,
                    'cantidad' => $cantidad,
                    'imagen' => $imagen
                ];
            }
        }
    }

    if ($accion === 'eliminar') {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] -= 1;
            if ($_SESSION['carrito'][$id]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
    }

    if ($accion === 'vaciar') {
        $_SESSION['carrito'] = [];
    }

    header("Location: tienda.php");
    exit;
}
