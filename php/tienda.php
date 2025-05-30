<?php
session_start();
include("../connection/db.php");

// Obtener filtros si existen
$filtro_categoria = $_GET['categoria'] ?? null;
$filtro_marca = $_GET['marca'] ?? null;

// Construcción de la consulta con filtros
$sql = "SELECT id_producto, nombre, descripcion, precio, imagen_url FROM productos";
$condiciones = [];

if ($filtro_categoria) {
  $condiciones[] = "categoría = '" . $conexion->real_escape_string($filtro_categoria) . "'";
}
if ($filtro_marca) {
  $condiciones[] = "marca = '" . $conexion->real_escape_string($filtro_marca) . "'";
}
if (!empty($condiciones)) {
  $sql .= " WHERE " . implode(" AND ", $condiciones);
}
$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tienda</title>
  <link rel="stylesheet" href="../css/tienda.css">
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
          <a href="citas.php">Mis pedidos</a>
          <a href="logout.php">Cerrar Sesión</a>
        <?php else: ?>
          <a href="login.php">Iniciar Sesión</a>
          <a href="register.php">Registrarse</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<main class="main-container">
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

  <section class="product-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="product">
        <img src="../<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
        <h4><?php echo htmlspecialchars($row['nombre']); ?></h4>
        <p class="descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>
        <p class="precio"><?php echo number_format($row['precio'], 2); ?>€</p>

        <div class="cantidad-box">
          <button class="qty-btn">-</button>
          <input type="number" class="cantidad" value="0" min="0" readonly>
          <button class="qty-btn">+</button>
        </div>
        <button class="add-btn">Añadir a la cesta</button>
      </div>
    <?php endwhile; ?>
  </section>
</main>

<footer class="main-footer">
  <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
</footer>

<script src="../javascript/tienda.js"></script>
</body>
</html>
