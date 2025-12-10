<?php
session_start();
include("db.php");

/* ============================================================
   API REST
============================================================ */
if (isset($_GET['api'])) {

    if ($_GET['api'] === 'servicios') {
        $resultado = $conexion->query("SELECT id_servicio, nombre FROM servicios");
        $servicios = [];

        while ($fila = $resultado->fetch_assoc()) {
            $servicios[] = $fila;
        }

        header('Content-Type: application/json');
        echo json_encode($servicios);
        exit;
    }

    if ($_GET['api'] === 'descripcion' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conexion->prepare("SELECT descripcion FROM servicios WHERE id_servicio = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($descripcion);
        echo $stmt->fetch() ? $descripcion : "Descripción no disponible.";
        exit;
    }

    if ($_GET['api'] === 'comprobar_cita') {
        $fecha = $_GET['fecha'];
        $hora = $_GET['hora'];
        $servicio = intval($_GET['servicio']);

        $stmt = $conexion->prepare("SELECT id_cita FROM citas WHERE fecha = ? AND hora = ? AND id_servicio = ?");
        $stmt->bind_param("ssi", $fecha, $hora, $servicio);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "ocupado";
            exit;
        }

        $stmt2 = $conexion->prepare("SELECT COUNT(*) FROM citas WHERE fecha = ?");
        $stmt2->bind_param("s", $fecha);
        $stmt2->execute();
        $stmt2->bind_result($total);
        $stmt2->fetch();

        echo ($total >= 8) ? "completo" : "libre";
        exit;
    }

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

        echo json_encode($citas);
        exit;
    }
}



/* ============================================================
   FORMULARIOS
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? "";


    /* ============================================================
           REGISTRO DE USUARIO
    ============================================================ */
    if ($accion === "registro") {

        $nombre = trim($_POST['nombre']);
        $apellidos = trim($_POST['apellidos']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $direccion = trim($_POST['direccion']);
        $telefono = trim($_POST['telefono']);

        // Validación de contraseña fuerte
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            echo "<script>
                sessionStorage.setItem('mensajeRegistro', '❌ La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula y un número.');
                sessionStorage.setItem('tipoMensaje', 'error');
                window.location.href = '../php/register.php';
            </script>";
            exit;
        }

        // Contraseña cifrada
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario
        $stmt = $conexion->prepare("
            INSERT INTO usuarios (nombre, apellidos, email, contraseña, direccion, telefono, tipo_usuario)
            VALUES (?, ?, ?, ?, ?, ?, 'cliente')
        ");
        $stmt->bind_param("ssssss", $nombre, $apellidos, $email, $password_hash, $direccion, $telefono);

        if ($stmt->execute()) {

            /* ---- Enviar correo al administrador ---- */
            $para = "mariagarvid2000@gmail.com";
            $asunto = "Nuevo registro en Beauty";
            $mensaje = "El usuario $nombre se ha registrado.\n\nBienvenido a Beauty ❤️";
            $headers = "From: Beauty <no-reply@beauty.com>\r\nContent-Type: text/plain; charset=UTF-8";

            mail($para, $asunto, $mensaje, $headers);

            echo "<script>
                sessionStorage.setItem('mensajeRegistro', '✅ Usuario registrado correctamente');
                sessionStorage.setItem('tipoMensaje', 'exito');
                window.location.href = '../php/login.php';
            </script>";
            exit;

        } else {
            echo "<script>
                sessionStorage.setItem('mensajeRegistro', '❌ El correo ya está registrado.');
                sessionStorage.setItem('tipoMensaje', 'error');
                window.location.href = '../php/register.php';
            </script>";
            exit;
        }
    }



    /* ============================================================
           LOGIN
    ============================================================ */
    if ($accion === "login") {

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conexion->prepare("SELECT id_usuario, contraseña, tipo_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {

            $stmt->bind_result($id_usuario, $hash, $tipo);
            $stmt->fetch();

            $correcto = ($tipo === "admin")
                ? ($password === $hash)
                : password_verify($password, $hash);

            if ($correcto) {

                $_SESSION['id_usuario'] = $id_usuario;
                $_SESSION['tipo_usuario'] = $tipo;

                header("Location: " . ($tipo === "admin" ? "../php/admin/admin.php" : "../php/index.php"));
                exit;
            }

            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
            exit;
        }

        echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
        exit;
    }



    /* ============================================================
           REGISTRAR CITA
    ============================================================ */
    if ($accion === "cita") {

        if (!isset($_SESSION['id_usuario'])) {
            echo "<script>alert('Debes iniciar sesión para reservar.'); window.location.href='../php/login.php';</script>";
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        $servicio = intval($_POST['servicio']);
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $notas = trim($_POST['notas']);
        $estado = "pendiente";

        /* --- Obtener nombre del usuario --- */
        $stmtUser = $conexion->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ?");
        $stmtUser->bind_param("i", $id_usuario);
        $stmtUser->execute();
        $stmtUser->bind_result($nombre_usuario);
        $stmtUser->fetch();
        $stmtUser->close();

        /* --- Comprobar cita repetida --- */
        $stmt = $conexion->prepare("SELECT id_cita FROM citas WHERE fecha = ? AND hora = ? AND id_servicio = ?");
        $stmt->bind_param("ssi", $fecha, $hora, $servicio);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>
                sessionStorage.setItem('mensajeCita', '❌ La hora ya está ocupada para ese servicio.');
                sessionStorage.setItem('tipoMensaje', 'error');
                window.location.href = '../php/agenda.php';
            </script>";
            exit;
        }

        /* --- Insertar cita --- */
        $stmt = $conexion->prepare("
            INSERT INTO citas (id_usuario, id_servicio, fecha, hora, estado, notas)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissss", $id_usuario, $servicio, $fecha, $hora, $estado, $notas);
        $stmt->execute();

        /* ============================================================
               ENVIAR CORREO AL ADMIN CON EL MENSAJE EXACTO
        ============================================================ */
        $para = "mariagarvid2000@gmail.com";
        $asunto = "Nueva cita registrada";
        $mensaje = "Gracias por su cita, $nombre_usuario.\n\n".
                   "Se le volverá a mandar un correo si su cita ha sido confirmada.\n\n".
                   "Fecha: $fecha\n".
                   "Hora: $hora\n";

        $headers = "From: Beauty <no-reply@beauty.com>\r\nContent-Type: text/plain; charset=UTF-8";

        mail($para, $asunto, $mensaje, $headers);

        echo "<script>
            sessionStorage.setItem('mensajeCita', '✅ Cita registrada correctamente. Serás redirigido en 5 segundos...');
            sessionStorage.setItem('tipoMensaje', 'exito');
            window.location.href = '../php/agenda.php';
        </script>";
        exit;
    }
}

$conexion->close();
?>
