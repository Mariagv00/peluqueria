<?php
session_start();
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
        <!-- Ícono del carrito -->
        <img src="../img/carrito.jpg" alt="Carrito" class="cart-icon">
        
        <!-- Ícono de perfil -->
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

  <!-- Contenido principal -->
  <main class="main-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <p><strong>APARATOS ELÉCTRICOS:</strong><br>
        Secadores<br>
        Planchas<br>
        Tenacillas</p>
      <p><strong>CUIDADO CAPILAR:</strong><br>
        Tintes<br>
        Mascarillas<br>
        Champús</p>
      <p><strong>MARCAS:</strong><br>
        Kadux<br>
        Kerastase<br>
        Pantene</p>
    </aside>

    <!-- Galería de productos -->
    <section class="product-grid">
      <div class="product">IMAGEN1</div>
      <div class="product">IMAGEN2</div>
      <div class="product">IMAGEN3</div>
      <div class="product">IMAGEN4</div>
      <div class="product">IMAGEN5</div>
      <div class="product">IMAGEN6</div>
    </section>
  </main>

  <footer class="main-footer">
    <p>FOOTER</p>
  </footer>

  <script src="../javascript/tienda.js"></script>
</body>
</html>
