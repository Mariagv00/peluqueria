<?php
session_start();
require '../vendor/autoload.php'; // PHPMailer

require_once '../libs/fpdf/fpdf.php'; // FPDF clásico

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../connection/db.php");

// Validar usuario y carrito
if (!isset($_SESSION['id_usuario']) || empty($_SESSION['carrito'])) {
    echo "<script>alert('Debes iniciar sesión y tener productos en la cesta.'); window.location.href='tienda.php';</script>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$carrito = $_SESSION['carrito'];
$total = 0;

// Calcular total
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagar'])) {

    $fecha_pedido = date("Y-m-d H:i:s");

    // Insertar pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (id_usuario, fecha_pedido, total) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id_usuario, $fecha_pedido, $total);
    $stmt->execute();
    $id_pedido = $stmt->insert_id;
    $stmt->close();

    // Guardar detalles y preparar línea de factura
    $lineas = [];

    foreach ($carrito as $id_producto => $item) {
        $nombre = $item['nombre'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];
        $subtotal = $precio * $cantidad;

        $lineas[] = [$nombre, $cantidad, $precio, $subtotal];

        // Insertar detalle
        $stmtDetalle = $conexion->prepare("INSERT INTO detalle_pedido (id_pedido, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtDetalle->bind_param("isidd", $id_pedido, $nombre, $cantidad, $precio, $subtotal);
        $stmtDetalle->execute();
        $stmtDetalle->close();

        // Actualizar stock
        $stmtStock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
        $stmtStock->bind_param("ii", $cantidad, $id_producto);
        $stmtStock->execute();
        $stmtStock->close();
    }

    // Crear carpeta facturas si no existe
    if (!file_exists("../facturas")) {
        mkdir("../facturas", 0777, true);
    }

    // --------------------------
    // CREAR FACTURA PDF
    // --------------------------
    $pdf = new FPDF();
    $pdf->AddPage();

    // Encabezado
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 10, 'Factura - Beauty', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Fecha: ' . $fecha_pedido, 0, 1);
    $pdf->Ln(5);

    // Encabezado tabla
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 10, 'Producto', 1);
    $pdf->Cell(30, 10, 'Cantidad', 1);
    $pdf->Cell(40, 10, 'Precio', 1);
    $pdf->Cell(40, 10, 'Subtotal', 1);
    $pdf->Ln();

    // Contenido tabla
    $pdf->SetFont('Arial', '', 12);
    foreach ($lineas as [$nombre, $cantidad, $precio, $subtotal]) {
        $pdf->Cell(80, 10, utf8_decode($nombre), 1);
        $pdf->Cell(30, 10, $cantidad, 1);
        $pdf->Cell(40, 10, number_format($precio, 2) . ' ' . chr(128), 1);     // €
        $pdf->Cell(40, 10, number_format($subtotal, 2) . ' ' . chr(128), 1);   // €
        $pdf->Ln();
    }

    // Total
    $pdf->Cell(150, 10, 'Total', 1);
    $pdf->Cell(40, 10, number_format($total, 2) . ' ' . chr(128), 1);

    // Guardar PDF
    $pdfPath = "../facturas/factura_" . $id_pedido . ".pdf";
    $pdf->Output("F", $pdfPath);

    // --------------------------
    // ENVIAR FACTURA POR EMAIL
    // --------------------------
    $mail = new PHPMailer(true);
    try {
        $mail->setFrom('no-reply@beauty.com', 'Beauty');
        $mail->addAddress("mariagarvid2000@gmail.com");

        $mail->Subject = "Factura de su compra";
        $mail->Body =
            "Gracias por su compra.\n\n" .
            "Adjuntamos su factura.\n" .
            "Total pagado: " . number_format($total, 2) . " €";

        $mail->addAttachment($pdfPath);

        $mail->send();

    } catch (Exception $e) {
        error_log("Error enviando correo: " . $mail->ErrorInfo);
    }

    // Vaciar carrito
    $_SESSION['carrito'] = [];

    // --------------------------
    // MENSAJE DE ÉXITO
    // --------------------------
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Exitoso</title>
    <style>
        body {
            background-color: #f1dddb;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .mensaje {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .loader {
            margin: 20px auto;
            border: 5px solid #ccc;
            border-top: 5px solid #843e3c;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 { color: #843e3c; }
    </style>
    <script>
        setTimeout(() => { window.location.href = 'tienda.php'; }, 5000);
    </script>
</head>
<body>
    <div class="mensaje">
        <h2>¡Gracias por tu compra!</h2>
        <p>Tu pedido ha sido confirmado correctamente.</p>
        <div class="loader"></div>
        <p>Serás redirigido a la tienda en 5 segundos...</p>
    </div>
</body>
</html>
HTML;

    exit;
}
?>

<!-- FORMULARIO DE PAGO -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago con tarjeta</title>
    <link rel="stylesheet" href="../css/pagos.css">
</head>
<body>

<div class="paypal-box">
    <div class="paypal-header">
        <img src="https://www.paypalobjects.com/webstatic/icon/pp258.png" class="paypal-logo">
        <span><strong>Pagar con <span class="paypal-blue">PayPal</span></strong> o tarjeta</span>
    </div>

    <form method="post" action="pago.php">

        <div class="form-group">
            <label>Nombre en la tarjeta</label>
            <input type="text" name="nombre" required>
        </div>

        <div class="form-group">
            <label>Número de tarjeta</label>
            <input type="text" name="tarjeta" maxlength="16" pattern="\d{16}" required>
        </div>

        <div class="form-group">
            <label>Fecha caducidad (MM/AA)</label>
            <input type="text" name="caducidad" pattern="\d{2}/\d{2}" required>
        </div>

        <div class="form-group">
            <label>CVV</label>
            <input type="text" name="cvv" maxlength="4" pattern="\d{3,4}" required>
        </div>

        <div class="total">Total: <?= number_format($total, 2) ?> €</div>

        <button type="submit" name="pagar" class="btn-paypal">Pagar con PayPal</button>

        <div class="card-logos">
            <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg">
        </div>

    </form>
</div>

</body>
</html>
