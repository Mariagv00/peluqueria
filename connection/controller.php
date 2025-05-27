<?php
session_start();
include("db.php");

// ------------------- API REST -------------------
if (isset($_GET['api'])) {

    // Obtener todos los servicios
    if ($_GET['api'] === 'servicios') {
        $resultado = $conexion->query("SELECT id_servicio, nombre FROM servicios");
        $servicios = [];

        while ($fila = $resultado->fetch_assoc()) {
            $servicios[] = $fila;
        }

        header('Content-Type: application/json');
        echo json_encode($servicios);
        $conexion->close();
        exit;
    }

    // Obtener descripción de un servicio
    if ($_GET['api'] === 'descripcion' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conexion->prepare("SELECT descripcion FROM servicios WHERE id_servicio = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($descripcion);
        if ($stmt->fetch()) {
            echo $descripcion;
        } else {
            echo "Descripción no disponible.";
        }
        $stmt->close();
        $conexion->close();
        exit;
    }

    // Comprobar disponibilidad de cita
    if ($_GET['api'] === 'comprobar_cita') {
        $fecha = $_GET['fecha'] ?? '';
        $hora = $_GET['hora'] ?? '';
        $id_servicio = intval($_GET['servicio']);

        $stmt = $conexion->prepare("SELECT id_cita FROM citas WHERE fecha = ? AND hora = ? AND id_servicio = ?");
        $stmt->bind_param("ssi", $fecha, $hora, $id_servicio);
        $stmt->execute();
        $stmt->store_result();

        echo ($stmt->num_rows > 0) ? "ocupado" : "libre";

        $stmt->close();
        $conexion->close();
        exit;
    }

    // Obtener citas del usuario
    if ($_GET['api'] === 'citas') {
        if (!isset($_SESSION['id_usuario'])) {
            http_response_code(401);
            echo json_encode(["error" => "No autorizado"]);
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];

        $sql = "SELECT c.fecha, c.hora, s.nombre AS servicio, c.estado, c.notas
                FROM citas c
                INNER JOIN servicios s ON c.id_servicio = s.id_servicio
                WHERE c.id_usuario = ?
                ORDER BY c.fecha, c.hora";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $citas = [];
        while ($fila = $result->fetch_assoc()) {
            $citas[] = $fila;
        }

        header('Content-Type: application/json');
        echo json_encode($citas);
        $stmt->close();
        $conexion->close();
        exit;
    }
}

// ------------------- FORMULARIOS -------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';

    // Registro de usuario
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
                echo "<script>alert('Usuario registrado con éxito'); window.location.href='../php/login.php';</script>";
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

    // Login
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

    // Registro de cita
    elseif ($accion === "cita") {
        if (!isset($_SESSION['id_usuario'])) {
            echo "<script>alert('Debes iniciar sesión para reservar.'); window.location.href='../php/login.php';</script>";
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_servicio = intval($_POST['servicio']);
        $fecha = trim($_POST['fecha']);
        $hora = trim($_POST['hora']);
        $notas = trim($_POST['notas']);
        $estado = "pendiente";

        // Verificar si ya hay una cita para esa fecha y hora
        $verificar = $conexion->prepare("SELECT id_cita FROM citas WHERE fecha = ? AND hora = ?");
        $verificar->bind_param("ss", $fecha, $hora);
        $verificar->execute();
        $verificar->store_result();

        if ($verificar->num_rows > 0) {
            echo "<script>alert('Ya hay una cita registrada para esa fecha y hora. Elige otro horario.'); window.history.back();</script>";
            $verificar->close();
            exit;
        }
        $verificar->close();

        // Insertar nueva cita
        $stmt = $conexion->prepare("INSERT INTO citas (id_usuario, id_servicio, fecha, hora, estado, notas) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $id_usuario, $id_servicio, $fecha, $hora, $estado, $notas);

        if ($stmt->execute()) {
            // Obtener nombre usuario
            $queryUser = $conexion->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ?");
            $queryUser->bind_param("i", $id_usuario);
            $queryUser->execute();
            $queryUser->bind_result($nombreUsuario);
            $queryUser->fetch();
            $queryUser->close();

            // Simular envío de correo
            $to = "mariagarvid2000@gmail.com";
            $subject = "Nueva cita registrada en Beauty";
            $message = "Gracias por su cita, $nombreUsuario.\n\n";
            $message .= "Se le volverá a mandar un correo si su cita ha sido confirmada.\n";
            $message .= "Fecha: $fecha\nHora: $hora\n";

            $headers = "From: Beauty <beauty@gmail.com>\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8";

            mail($to, $subject, $message, $headers);

            echo "<script>alert('Cita registrada correctamente.'); window.location.href='../php/index.php';</script>";
        } else {
            echo "<script>alert('Error al registrar la cita: " . $stmt->error . "'); window.history.back();</script>";
        }

        $stmt->close();
    }

    // Cancelar cita
    elseif ($accion === "cancelar_cita") {
        if (!isset($_SESSION['id_usuario'])) {
            echo "<script>alert('Debes iniciar sesión.'); window.location.href='../php/login.php';</script>";
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];

        $stmt = $conexion->prepare("DELETE FROM citas WHERE id_usuario = ? AND fecha = ? AND hora = ?");
        $stmt->bind_param("iss", $id_usuario, $fecha, $hora);

        if ($stmt->execute()) {
            echo "<script>alert('Cita cancelada.'); window.location.href='../php/citas.php';</script>";
        } else {
            echo "<script>alert('Error al cancelar la cita.'); window.history.back();</script>";
        }

        $stmt->close();
    }
}

$conexion->close();
?>
