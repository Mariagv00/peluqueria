<?php
session_start();
include("../connection/db.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Guardar cambios si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $apellidos = $conexion->real_escape_string($_POST['apellidos']);
    $direccion = $conexion->real_escape_string($_POST['direccion']);
    $telefono = $conexion->real_escape_string($_POST['telefono']);
    $email = $conexion->real_escape_string($_POST['email']);

    $sql = "UPDATE usuarios SET nombre = ?, apellidos = ?, direccion = ?, telefono = ?, email = ? WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $apellidos, $direccion, $telefono, $email, $id_usuario);
    $stmt->execute();
    $stmt->close();

    $mensaje = "Datos actualizados correctamente.";
}

// Obtener datos actuales del usuario
$stmt = $conexion->prepare("SELECT nombre, apellidos, direccion, telefono, email FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($nombre, $apellidos, $direccion, $telefono, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil de <?= htmlspecialchars($nombre) ?></title>
  <link rel="stylesheet" href="../css/perfil.css">
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
      <img src="../img/perfil.png" alt="Perfil" class="profile-icon" id="profileIcon">
      <div class="dropdown" id="dropdownMenu">
        <a href="perfil.php">Perfil</a>
        <a href="citas.php">Mis citas</a>
        <a href="logout.php">Cerrar Sesión</a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="perfil-container">

    <h2><?= htmlspecialchars($nombre) ?></h2>

    <?php if (isset($mensaje)) : ?>
      <div class="mensaje" id="mensaje"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="post">

      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($nombre) ?>" required>

      <label for="apellidos">Apellidos:</label>
      <input type="text" name="apellidos" id="apellidos" value="<?= htmlspecialchars($apellidos) ?>" required>

      <label for="direccion">Dirección:</label>
      <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($direccion) ?>" required>

      <label for="telefono">Teléfono:</label>
      <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($telefono) ?>" required>

      <label for="email">Correo electrónico:</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

      <button type="submit" class="btn-guardar">Guardar cambios</button>
    </form>

  </div>
</main>

<footer class="main-footer">
  <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
</footer>

<script>
  const profileIcon = document.getElementById("profileIcon");
  const dropdownMenu = document.getElementById("dropdownMenu");

  if (profileIcon && dropdownMenu) {
    profileIcon.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
    });

    document.addEventListener("click", function (e) {
      if (!dropdownMenu.contains(e.target) && e.target !== profileIcon) {
        dropdownMenu.classList.remove("show");
      }
    });
  }

  // Ocultar mensaje a los 5 segundos
  const mensaje = document.getElementById("mensaje");
  if (mensaje) {
    setTimeout(() => {
      mensaje.style.opacity = "0";
      setTimeout(() => mensaje.style.display = "none", 500);
    }, 5000);
  }
</script>

</body>
</html>
