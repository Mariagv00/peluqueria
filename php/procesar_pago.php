<?php
session_start();
require_once '../libs/dompdf/vendor/autoload.php'; // Usando Composer correctamente

use Dompdf\Dompdf;
use Dompdf\Options;

include("../connection/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['carrito']) && isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $total = 0;
    $fecha_pedido = date("Y-m-d H:i:s");

    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    // 1. Insertar pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (id_usuario, fecha_pedido, total) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id_usuario, $fecha_pedido, $total);
    $stmt->execute();
    $id_pedido = $stmt->insert_id;
    $stmt->close();

    // 2. Insertar detalle_pedido y construir HTML
    $facturaHTML = "<h2>Factura del pedido </h2>";
    $facturaHTML .= "<p><strong>Fecha:</strong> $fecha_pedido</p>";
    $facturaHTML .= "<table border='1' cellpadding='6' cellspacing='0' style='width:100%; border-collapse:collapse;'>
                      <thead><tr>
                        <th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th>
                      </tr></thead><tbody>";

    foreach ($_SESSION['carrito'] as $item) {
        $nombre = $item['nombre'];
        $cantidad = $item['cantidad'];
        $precio_unitario = $item['precio'];
        $subtotal = $precio_unitario * $cantidad;

        $stmt = $conexion->prepare("INSERT INTO detalle_pedido (id_pedido, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isidd", $id_pedido, $nombre, $cantidad, $precio_unitario, $subtotal);
        $stmt->execute();
        $stmt->close();

        $facturaHTML .= "<tr>
                          <td>$nombre</td>
                          <td>$cantidad</td>
                          <td>" . number_format($precio_unitario, 2) . " €</td>
                          <td>" . number_format($subtotal, 2) . " €</td>
                        </tr>";
    }

    $facturaHTML .= "</tbody></table>";
    $facturaHTML .= "<p style='text-align:right; font-size:16px;'><strong>Total: " . number_format($total, 2) . " €</strong></p>";

    // 3. Generar PDF con Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($facturaHTML);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Asegurar directorio de facturas
    $pdfDir = "../facturas";
    if (!file_exists($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    $pdfFile = "$pdfDir/factura_$id_pedido.pdf";
    file_put_contents($pdfFile, $dompdf->output());

    // 4. Enviar correo con PDF
    $to = "mariagarvid2000@gmail.com"; // O el email del usuario si lo tienes
    $subject = "Confirmación de pedido - Beauty";
    $message = "Gracias por su pedido. Adjunto su factura.";
    $separator = md5(time());
    $eol = PHP_EOL;

    $headers = "From: Beauty <no-reply@beauty.com>" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    // Email body
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"utf-8\"" . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
    $body .= $message . $eol;

    // Attachment
    $attachment = chunk_split(base64_encode(file_get_contents($pdfFile)));
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/pdf; name=\"factura_$id_pedido.pdf\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment; filename=\"factura_$id_pedido.pdf\"" . $eol . $eol;
    $body .= $attachment . $eol;
    $body .= "--" . $separator . "--";

    mail($to, $subject, $body, $headers);

    // 5. Limpiar carrito y redirigir
    $_SESSION['carrito'] = [];
    header("Location: pago_exitoso.php");
    exit;
} else {
    echo "<script>alert('Error al procesar el pago.'); window.location.href='tienda.php';</script>";
}
?>
