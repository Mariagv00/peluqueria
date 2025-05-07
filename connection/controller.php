<?php
session_start();
include("db.php");

// Redirigir si no se envía el formulario
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../php/index.php");
    exit;
}

$accion = $_POST['accion'] ?? '';

// ---------------- REGISTRO ----------------
if ($accion === "registro") {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telefono = trim($_POST['telefono']);
    $fecha_registro = date("Y-m-d H:i:s");
    $tipo_usuario = "cliente";

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellidos, email, contraseña, teléfono, fecha_registro, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nombre, $apellidos, $email, $password, $telefono, $fecha_registro, $tipo_usuario);

    try {
        if ($stmt->execute()) {
            echo "<script>alert('Usuario registrado con éxito'); window.location.href='../pages/login.php';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        if (str_contains($e->getMessage(), "Duplicate entry")) {
            echo "<script>alert('El correo ya está registrado'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error al registrar usuario: " . $e->getMessage() . "'); window.history.back();</script>";
        }
    }

    $stmt->close();
}

// ---------------- LOGIN ----------------
elseif ($accion === "login") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT id_usuario, contraseña FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['contraseña'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            header("Location: ../php/index.php");
            exit;
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
    }

    $stmt->close();
}

$conexion->close();
?>
