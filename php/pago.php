<?php
session_start();
require_once '../libs/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include("../connection/db.php");

if (!isset($_SESSION['id_usuario']) || empty($_SESSION['carrito'])) {
  echo "<script>alert('Debes iniciar sesión y tener productos en la cesta para continuar.'); window.location.href='tienda.php';</script>";
  exit;
}

$id_usuario = $_SESSION['id_usuario'];
$carrito = $_SESSION['carrito'];
$total = 0;

// Si se envió el formulario de pago
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pagar'])) {
    $fecha_pedido = date("Y-m-d H:i:s");

    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    // 1. Insertar pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (id_usuario, fecha_pedido, total) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id_usuario, $fecha_pedido, $total);
    $stmt->execute();
    $id_pedido = $stmt->insert_id;
    $stmt->close();

    // 2. Insertar detalle y actualizar stock
    $facturaHTML = "<h2>Factura del pedido</h2>";
    $facturaHTML .= "<p><strong>Fecha:</strong> $fecha_pedido</p>";
    $facturaHTML .= "<table border='1' cellpadding='6' cellspacing='0' style='width:100%; border-collapse:collapse;'>
                      <thead><tr>
                        <th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th>
                      </tr></thead><tbody>";

    foreach ($carrito as $id_producto => $item) {
        $nombre = $item['nombre'];
        $cantidad = $item['cantidad'];
        $precio_unitario = $item['precio'];
        $subtotal = $precio_unitario * $cantidad;

        $stmt = $conexion->prepare("INSERT INTO detalle_pedido (id_pedido, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isidd", $id_pedido, $nombre, $cantidad, $precio_unitario, $subtotal);
        $stmt->execute();
        $stmt->close();

        $stmtStock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
        $stmtStock->bind_param("ii", $cantidad, $id_producto);
        $stmtStock->execute();
        $stmtStock->close();

        $facturaHTML .= "<tr>
                          <td>$nombre</td>
                          <td>$cantidad</td>
                          <td>" . number_format($precio_unitario, 2) . " €</td>
                          <td>" . number_format($subtotal, 2) . " €</td>
                        </tr>";
    }

    $facturaHTML .= "</tbody></table>";
    $facturaHTML .= "<p style='text-align:right; font-size:16px;'><strong>Total: " . number_format($total, 2) . " €</strong></p>";

    // 3. Generar PDF
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($facturaHTML);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfDir = "../facturas";
    if (!file_exists($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    $pdfFile = "$pdfDir/factura_$id_pedido.pdf";
    file_put_contents($pdfFile, $dompdf->output());

    // 4. Enviar correo
    $to = "mariagarvid2000@gmail.com";
    $subject = "Confirmación de pedido - Beauty";
    $message = "Gracias por su pedido. Adjunto su factura.";
    $separator = md5(time());
    $eol = PHP_EOL;

    $headers = "From: Beauty <no-reply@beauty.com>" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"utf-8\"" . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
    $body .= $message . $eol;

    $attachment = chunk_split(base64_encode(file_get_contents($pdfFile)));
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/pdf; name=\"factura_$id_pedido.pdf\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment; filename=\"factura_$id_pedido.pdf\"" . $eol . $eol;
    $body .= $attachment . $eol;
    $body .= "--" . $separator . "--";

    mail($to, $subject, $body, $headers);

    $_SESSION['carrito'] = [];
    header("Location: pago_exitoso.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago con tarjeta</title>
  <link rel="stylesheet" href="../css/pagos.css">
</head>
<body>
  <div class="container">
    <h2>Introduce tus datos de tarjeta</h2>

    <form method="post" action="pago.php">
      <div class="form-group">
        <label for="nombre">Nombre en la tarjeta</label>
        <input type="text" name="nombre" id="nombre" required>
      </div>

      <div class="form-group">
        <label for="tarjeta">Número de tarjeta</label>
        <input type="text" name="tarjeta" id="tarjeta" maxlength="16" pattern="\d{16}" required>
      </div>

      <div class="form-group">
        <label for="caducidad">Fecha de caducidad (MM/AA)</label>
        <input type="text" name="caducidad" id="caducidad" placeholder="MM/AA" pattern="\d{2}/\d{2}" required>
      </div>

      <div class="form-group">
        <label for="cvv">CVV</label>
        <input type="text" name="cvv" id="cvv" maxlength="4" pattern="\d{3,4}" required>
      </div>

      <div class="total">
        Total: 
        <?php
          foreach ($carrito as $producto) {
            $total += $producto['precio'] * $producto['cantidad'];
          }
          echo number_format($total, 2) . " €";
        ?>
      </div>

      <button type="submit" name="pagar" class="btn-pagar">Pagar</button>
    </form>
  </div>
</body>
</html>
