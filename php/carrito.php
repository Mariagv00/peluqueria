<?php
session_start();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];

    // Añadir al carrito
    if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += 1;
        } else {
            $_SESSION['carrito'][$id] = [
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1,
                'imagen' => $imagen
            ];
        }
    }

    // Eliminar un producto
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] -= 1;
            if ($_SESSION['carrito'][$id]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
    }

    // Vaciar carrito completo
    if (isset($_POST['accion']) && $_POST['accion'] === 'vaciar') {
        $_SESSION['carrito'] = [];
    }

    header("Location: tienda.php");
    exit;
}
