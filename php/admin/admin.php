<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="../../css/admin/admin.css">
</head>
<body>

  <!-- Navbar -->
  <header>
    <nav>
      <div class="menu">
        <img src="../../img/logo.png" alt="Logo" class="logo">
        <a href="admin.php">INICIO</a>
        <a href="usuarios.php">USUARIOS</a>
        <a href="citasAdmin.php">CITAS</a>
        <a href="productos.php">PRODUCTOS</a>
      </div>
      <div class="profile-container">
        <img src="../../img/perfil.png" alt="Perfil" class="profile-icon" id="profileIcon">
        <div class="dropdown" id="dropdownMenu">
          <a href="../logout.php">Cerrar Sesión</a>
        </div>
      </div>
    </nav>
  </header>

  <main class="admin-container">
    <h1 class="admin-title">Panel de Administración</h1>
    <div class="admin-links">
      <a href="usuarios.php">Gestión de Usuarios</a>
      <a href="citasAdmin.php">Gestión de Citas</a>
      <a href="productos.php">Gestión de Productos</a>
    </div>
  </main>

  <!-- Footer -->
  <footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
  </footer>

  <script src="../../javascript/index.js"></script>
</body>
</html>
