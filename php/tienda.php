<?php
session_start();
include "../connection/db.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Tienda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/tienda.css">
</head>

<body>

  <header class="navbar navbar-expand-lg bg-c47e6d py-3 px-4">
    <div class="container-fluid justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <img src="../img/logo.png" alt="Logo" class="logo">
        <a class="nav-link text-white fw-bold" href="index.php">INICIO</a>
        <a class="nav-link text-white fw-bold" href="agenda.php">AGENDA</a>
        <a class="nav-link text-white fw-bold" href="tienda.php">TIENDA</a>
      </div>
      <div class="d-flex align-items-center gap-3 position-relative">
        <img src="../img/carrito.png" alt="Carrito" class="cart-icon" id="cartIcon">
        <img src="../img/perfil.png" alt="Perfil" class="profile-icon" id="profileIcon">
        <div class="dropdown" id="dropdownMenu">

          <?php if (isset($_SESSION['id_usuario'])): ?>
            <a href="citas.php">Mis citas</a>
            <a class="dropdown-item" href="pedidos.php">Mis pedidos</a>
            <a href="perfil.php">Perfil</a>
            <a class="dropdown-item" href="logout.php">Cerrar Sesión</a>
          <?php else: ?>
            <a class="dropdown-item" href="login.php">Iniciar Sesión</a>
            <a class="dropdown-item" href="register.php">Registrarse</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <main class="main-container d-flex gap-4">
    <aside class="sidebar">
      <p><strong>APARATOS ELÉCTRICOS:</strong><br>
        <a href="?categoria=Secadores">Secadores</a><br>
        <a href="?categoria=Planchas">Planchas</a><br>
        <a href="?categoria=Tenacillas">Tenacillas</a>
      </p>
      <p><strong>CUIDADO CAPILAR:</strong><br>
        <a href="?categoria=Tintes">Tintes</a><br>
        <a href="?categoria=Mascarillas">Mascarillas</a><br>
        <a href="?categoria=Champús">Champús</a>
      </p>
      <p><strong>MARCAS:</strong><br>
        <a href="?marca=Remington">Remington</a><br>
        <a href="?marca=BaByliss">BaByliss</a><br>
        <a href="?marca=Rowenta">Rowenta</a><br>
        <a href="?marca=Revlon">Revlon</a><br>
        <a href="?marca=Garnier">Garnier</a><br>
        <a href="?marca=L'Oreal">L'Oreal</a>
      </p>
    </aside>

    <?php include "cesta.php"; ?>

    <section class="product-grid">
      <?php
      $filtro = "";

      if (isset($_GET['categoria'])) {
        $categoria = $conexion->real_escape_string($_GET['categoria']);
        $filtro    = "WHERE categoría = '$categoria'";
      } elseif (isset($_GET['marca'])) {
        $marca  = $conexion->real_escape_string($_GET['marca']);
        $filtro = "WHERE marca = '$marca'";
      }

      $sql    = "SELECT id_producto, nombre, descripcion, precio, imagen_url FROM productos $filtro";
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
              <input type="number" name="cantidad" class="cantidad" value="1" min="1">
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

  <footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
  </footer>

  <script src="../javascript/tienda.js"></script>
  <script>
    function cambiarCantidad(btn, delta) {
      const input = btn.parentElement.querySelector('.cantidad');
      let val = parseInt(input.value);
      if (delta === -1 && val > 1) val--;
      else if (delta === 1) val++;
      input.value = val;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>