<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../../connection/db.php");

// Procesar edición
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editar_usuario'])) {
    $id = intval($_POST['id']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);

    $stmt = $conexion->prepare("UPDATE usuarios SET email = ?, direccion = ?, telefono = ? WHERE id_usuario = ?");
    $stmt->bind_param("sssi", $email, $direccion, $telefono, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: usuarios.php?mensaje=modificado");
    exit;
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $idEliminar);
    $stmt->execute();
    $stmt->close();

    header("Location: usuarios.php?mensaje=eliminado");
    exit;
}

// Buscar usuarios
$busqueda = $_GET['busqueda'] ?? "";
$sql = "SELECT id_usuario, nombre, apellidos, email, direccion, telefono, fecha_registro 
        FROM usuarios 
        WHERE tipo_usuario = 'cliente'";
if (!empty($busqueda)) {
    $busqueda = $conexion->real_escape_string($busqueda);
    $sql .= " AND (nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%')";
}
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="stylesheet" href="../../css/admin/admin.css">
    <link rel="stylesheet" href="../../css/admin/usuarios.css">
</head>
<body>

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
    <h2 class="admin-title">Usuarios Registrados</h2>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="mensaje <?php echo $_GET['mensaje'] ?>">
            <?php if ($_GET['mensaje'] == 'modificado') echo "✅ Usuario modificado correctamente.";
                  elseif ($_GET['mensaje'] == 'eliminado') echo "🗑️ Usuario eliminado correctamente."; ?>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.mensaje').style.display = 'none';
            }, 5000);
        </script>
    <?php endif; ?>

    <form method="get" class="buscador-form">
        <input type="text" name="busqueda" placeholder="Buscar por nombre o email" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <table class="usuarios-table">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Apellidos</th>
                <th>Email</th><th>Dirección</th><th>Teléfono</th>
                <th>Fecha Registro</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                <form method="post">
                    <td><?= $row['id_usuario'] ?><input type="hidden" name="id" value="<?= $row['id_usuario'] ?>"></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['apellidos']) ?></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required></td>
                    <td><input type="text" name="direccion" value="<?= htmlspecialchars($row['direccion']) ?>" required></td>
                    <td><input type="text" name="telefono" value="<?= htmlspecialchars($row['telefono']) ?>" required></td>
                    <td><?= $row['fecha_registro'] ?></td>
                    <td>
                        <button type="submit" name="editar_usuario" class="editar-btn">Guardar</button>
                        <a href="?eliminar=<?= $row['id_usuario'] ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')" class="eliminar-btn">Eliminar</a>
                    </td>
                </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
</footer>
<script src="../../javascript/index.js"></script>
</body>
</html>
