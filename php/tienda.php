<?php
session_start();
include("../connection/db.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tienda</title>
  <link rel="stylesheet" href="../css/tienda.css">
</head>
<body>

  <!-- Header -->
  <header>
    <nav>
      <div class="menu">
        <img src="../img/logo.png" alt="Logo" class="logo">
        <a href="index.php">INICIO</a>
        <a href="agenda.php">AGENDA</a>
        <a href="tienda.php">TIENDA</a>
      </div>
      <div class="profile-container">
        <img src="../img/carrito.png" alt="Carrito" class="cart-icon" id="cartIcon">
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

  <!-- Contenido principal -->
  <main class="main-container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <p><strong>APARATOS ELÉCTRICOS:</strong><br>
        <a href="?categoria=Secadores">Secadores</a><br>
        <a href="?categoria=Planchas">Planchas</a><br>
        <a href="?categoria=Tenacillas">Tenacillas</a></p>
      <p><strong>CUIDADO CAPILAR:</strong><br>
        <a href="?categoria=Tintes">Tintes</a><br>
        <a href="?categoria=Mascarillas">Mascarillas</a><br>
        <a href="?categoria=Champús">Champús</a></p>
      <p><strong>MARCAS:</strong><br>
        <a href="?marca=Remington">Remington</a><br>
        <a href="?marca=BaByliss">BaByliss</a><br>
        <a href="?marca=Rowenta">Rowenta</a><br>
        <a href="?marca=Revlon">Revlon</a><br>
        <a href="?marca=Garnier">Garnier</a><br>
        <a href="?marca=L'Oreal">L'Oreal</a>
      </p>
    </aside>

    <!-- Panel del carrito -->
    <?php include("cesta.php"); ?>

    <!-- Galería de productos -->
    <section class="product-grid">
      <?php
      $filtro = "";

      if (isset($_GET['categoria'])) {
        $categoria = $conexion->real_escape_string($_GET['categoria']);
        $filtro = "WHERE categoría = '$categoria'";
      } elseif (isset($_GET['marca'])) {
        $marca = $conexion->real_escape_string($_GET['marca']);
        $filtro = "WHERE marca = '$marca'";
      }

      $sql = "SELECT id_producto, nombre, descripcion, precio, imagen_url FROM productos $filtro";
      $result = $conexion->query($sql);

      while ($row = $result->fetch_assoc()):
      ?>
        <div class="product">
          <img src="../<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
          <h4><?php echo htmlspecialchars($row['nombre']); ?></h4>
          <p class="descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>
          <p class="precio"><?php echo number_format($row['precio'], 2); ?>€</p>

          <form method="post" action="carrito.php">
            <div class="cantidad-box">
              <button type="button" class="qty-btn" onclick="cambiarCantidad(this, -1)">-</button>
              <input type="number" name="cantidad" class="cantidad" value="0" min="0" readonly>
              <button type="button" class="qty-btn" onclick="cambiarCantidad(this, 1)">+</button>
            </div>
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>">
            <input type="hidden" name="precio" value="<?php echo $row['precio']; ?>">
            <input type="hidden" name="imagen" value="<?php echo $row['imagen_url']; ?>">
            <button type="submit" class="add-btn">Añadir a la cesta</button>
          </form>
        </div>
      <?php endwhile; ?>
    </section>
  </main>

  <!-- Footer -->
  <footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
  </footer>

  <script src="../javascript/tienda.js"></script>
  <script>
    function cambiarCantidad(btn, delta) {
      const input = btn.parentElement.querySelector('.cantidad');
      let val = parseInt(input.value);
      if (delta === -1 && val > 0) val--;
      else if (delta === 1) val++;
      input.value = val;
    }
  </script>
</body>
</html>
