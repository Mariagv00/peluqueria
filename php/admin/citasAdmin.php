<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require '../../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../../connection/db.php");

// Procesar aceptación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cita'])) {

    $id_cita = intval($_POST['id_cita']);

    // Obtener datos de la cita para enviar correo
    $stmtInfo = $conexion->prepare("
        SELECT 
            u.nombre AS usuario, 
            u.email,
            s.nombre AS servicio, 
            c.fecha, 
            c.hora 
        FROM citas c
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        JOIN servicios s ON c.id_servicio = s.id_servicio
        WHERE c.id_cita = ?
    ");
    $stmtInfo->bind_param("i", $id_cita);
    $stmtInfo->execute();
    $stmtInfo->bind_result($usuario, $email_usuario, $servicio, $fecha, $hora);
    $stmtInfo->fetch();
    $stmtInfo->close();

    // Actualizar la cita como aceptada
    $stmt = $conexion->prepare("UPDATE citas SET estado = 'aceptada' WHERE id_cita = ?");
    $stmt->bind_param("i", $id_cita);
    $stmt->execute();
    $stmt->close();

    // --------------------------
    // ENVIAR CORREO DE CONFIRMACIÓN
    // --------------------------
    $mail = new PHPMailer(true);

    try {
        $mail->setFrom('no-reply@beauty.com', 'Beauty');
        $mail->addAddress("mariagarvid2000@gmail.com"); // destinatario fijo

        $mail->Subject = "Cita aceptada - $usuario";

        $mail->Body = 
            "Hola $usuario,\n\n" .
            "Tu cita ha sido *ACEPTADA*.\n\n" .
            "📅 Fecha: $fecha\n" .
            "⏰ Hora: $hora\n" .
            "💇 Servicio: $servicio\n\n" .
            "Gracias por confiar en Beauty.";

        $mail->send();

    } catch (Exception $e) {
        error_log("Error enviando correo de cita aceptada: " . $mail->ErrorInfo);
    }

    header("Location: citasAdmin.php?mensaje=aceptada");
    exit;
}

// Obtener citas
$hoy = date('Y-m-d');
$result = $conexion->query("
    SELECT c.id_cita, u.nombre AS usuario, s.nombre AS servicio, c.fecha, c.hora, c.estado
    FROM citas c
    JOIN usuarios u ON c.id_usuario = u.id_usuario
    JOIN servicios s ON c.id_servicio = s.id_servicio
    ORDER BY c.fecha, c.hora
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas</title>
    <link rel="stylesheet" href="../../css/admin/admin.css">
    <link rel="stylesheet" href="../../css/admin/citasAdmin.css">
</head>
<body>
<header>
    <nav>
        <div class="menu">
            <img src="../../img/logo.png" alt="Logo" class="logo">
            <a href="admin.php">INICIO</a>
            <a href="usuarios.php">USUARIOS</a>
            <a href="citasAdmin.php">CITAS</a>
            <a href="productos.php">PRODUCTOS</a>
        </div>
        <div class="profile-container">
            <img src="../../img/perfil.png" alt="Perfil" class="profile-icon" id="profileIcon">
            <div class="dropdown" id="dropdownMenu">
                <a href="../logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
</header>

<main class="admin-container">
    <h1 class="admin-title">Gestión de Citas</h1>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'aceptada'): ?>
        <div class="mensaje exito">✅ Cita aceptada y correo enviado correctamente.</div>
        <script>
            setTimeout(() => {
                const msg = document.querySelector('.mensaje');
                if (msg) msg.style.display = 'none';
            }, 5000);
        </script>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
        <tr>
            <th>Usuario</th>
            <th>Servicio</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($cita = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($cita['usuario']) ?></td>
                <td><?= htmlspecialchars($cita['servicio']) ?></td>
                <td><?= $cita['fecha'] ?></td>
                <td><?= $cita['hora'] ?></td>
                <td><?= ucfirst($cita['estado']) ?></td>
                <td>
                    <?php if ($cita['fecha'] >= $hoy && $cita['estado'] === 'pendiente'): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
                            <button type="submit" class="btn-aceptar">Aceptar Cita</button>
                        </form>
                    <?php else: ?>
                        <em></em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</main>

<footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
</footer>
<script src="../../javascript/index.js"></script>
</body>
</html>
