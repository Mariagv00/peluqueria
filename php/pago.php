<?php
session_start();

if (empty($_SESSION['carrito'])) {
  header("Location: tienda.php");
  exit;
}

$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago con tarjeta</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1dddb;
      padding: 40px;
    }
    .container {
      background: white;
      padding: 30px;
      max-width: 500px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      color: #843e3c;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      font-weight: bold;
      margin-bottom: 6px;
    }
    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .total {
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
      text-align: right;
    }
    .btn-pagar {
      background-color: #843e3c;
      color: white;
      border: none;
      padding: 12px;
      width: 100%;
      font-size: 16px;
      border-radius: 6px;
      margin-top: 20px;
      cursor: pointer;
    }
    .btn-pagar:hover {
      background-color: #6d302e;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Introduce tus datos de tarjeta</h2>

    <form action="procesar_pago.php" method="post">
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
          foreach ($_SESSION['carrito'] as $producto) {
            $total += $producto['precio'] * $producto['cantidad'];
          }
          echo number_format($total, 2) . "€";
        ?>
      </div>

      <button type="submit" class="btn-pagar">Pagar</button>
    </form>
  </div>
</body>
</html>
