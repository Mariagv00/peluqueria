<?php
session_start();
$_SESSION['carrito'] = [];
?>

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
    .mensaje h2 {
      color: #843e3c;
    }
    .mensaje a {
      display: inline-block;
      margin-top: 20px;
      background-color: #843e3c;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 6px;
    }
    .mensaje a:hover {
      background-color: #6d302e;
    }
  </style>
</head>
<body>
  <div class="mensaje">
    <h2>¡Gracias por tu compra!</h2>
    <p>Tu pedido se ha procesado correctamente.</p>
    <a href="tienda.php">Volver a la tienda</a>
  </div>
</body>
</html>
