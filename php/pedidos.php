<?php
session_start();
include("../connection/db.php");

if (!isset($_SESSION['id_usuario'])) {
  echo "<script>alert('Debes iniciar sesión para ver tus pedidos.'); window.location.href='login.php';</script>";
  exit;
}

$id_usuario = $_SESSION['id_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis pedidos</title>
  <link rel="stylesheet" href="../css/tienda.css">
  <style>
    .pedido {
      background: #fff;
      border: 1px solid #ccc;
      margin: 15px 0;
      padding: 15px;
      border-radius: 5px;
    }

    .pedido h3 {
      margin-bottom: 10px;
    }

    .pedido table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .pedido th, .pedido td {
      border: 1px solid #999;
      padding: 8px;
      text-align: center;
    }

    .pedido th {
      background-color: #eee;
    }

    .dropdown {
  display: none;
  position: absolute;
  top: 45px;
  right: 0;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  min-width: 150px;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.dropdown a {
  display: block;
  padding: 10px;
  color: #843e3c;
  text-decoration: none;
}

.dropdown a:hover {
  background-color: #f1dddb;
}

.dropdown.show {
  display: block;
}
  </style>
</head>
<body>

<header>
  <nav>
    <div class="menu">
      <img src="../img/logo.png" alt="Logo" class="logo">
      <a href="index.php">INICIO</a>
      <a href="agenda.php">AGENDA</a>
      <a href="tienda.php">TIENDA</a>
    </div>
    <div class="profile-container">
      <img src="../img/carrito.png" alt="Carrito" class="cart-icon">
      <img src="../img/perfil.png" alt="Perfil" class="profile-icon" id="profileIcon">
      <div class="dropdown" id="dropdownMenu">
          <?php if (isset($_SESSION['id_usuario'])): ?>
            <a href="pedidos.php">Mis pedidos</a>
            <a href="logout.php">Cerrar Sesión</a>
          <?php else: ?>
            <a href="login.php">Iniciar Sesión</a>
            <a href="register.php">Registrarse</a>
          <?php endif; ?>
        </div>
    </div>
  </nav>
</header>

<main class="main-container" style="flex-direction: column; margin-top: 100px; padding: 30px;">

  <h2>Mis pedidos</h2>

  <?php
  $query = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC";
  $stmt = $conexion->prepare($query);
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($pedido = $result->fetch_assoc()):
    $id_pedido = $pedido['id_pedido'];
    $fecha = $pedido['fecha_pedido'];
    $total = $pedido['total'];
    ?>

    <div class="pedido">
      <h3>Pedido <?= $fecha ?></h3>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $detalle = $conexion->query("SELECT * FROM detalle_pedido WHERE id_pedido = $id_pedido");
        while ($row = $detalle->fetch_assoc()):
        ?>
          <tr>
            <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
            <td><?= $row['cantidad'] ?></td>
            <td><?= number_format($row['precio_unitario'], 2) ?> €</td>
            <td><?= number_format($row['subtotal'], 2) ?> €</td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      <p><strong>Total del pedido:</strong> <?= number_format($total, 2) ?> €</p>
    </div>

  <?php endwhile;
  $stmt->close();
  ?>
</main>
 <script src="../javascript/index.js"></script>
</body>
</html>
