<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../../connection/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cita'])) {
    $id_cita = intval($_POST['id_cita']);

    // Obtener datos de la cita y del usuario
    $stmt = $conexion->prepare("
        SELECT c.fecha, c.hora, u.nombre 
        FROM citas c
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        WHERE c.id_cita = ?
    ");
    $stmt->bind_param("i", $id_cita);
    $stmt->execute();
    $stmt->bind_result($fecha, $hora, $nombre);
    $stmt->fetch();
    $stmt->close();

    if ($fecha && $hora && $nombre) {
        // Cambiar estado a confirmada
        $update = $conexion->prepare("UPDATE citas SET estado = 'confirmada' WHERE id_cita = ?");
        $update->bind_param("i", $id_cita);
        $update->execute();
        $update->close();

        // Enviar correo a dirección fija
        $to = "mariagarvid2000@gmail.com";
        $subject = "Confirmación de cita - Beauty";
        $message = "Hola $nombre,\n\nTu cita ha sido confirmada para la fecha: $fecha a las $hora.\n\n¡Gracias por confiar en nosotros!\n\nBeauty";
        $headers = "From: Beauty <no-reply@beauty.com>\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";

        mail($to, $subject, $message, $headers);

        echo "<script>alert('Cita confirmada y correo enviado.'); window.location.href='citasAdmin.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error: No se pudieron recuperar los datos de la cita.'); window.location.href='citasAdmin.php';</script>";
    }
} else {
    echo "<script>alert('Acceso no válido.'); window.location.href='citasAdmin.php';</script>";
}
