<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../../connection/db.php");

$busqueda = $_GET['busqueda'] ?? "";

$sql = "SELECT id_usuario, nombre, apellidos, email, teléfono, fecha_registro 
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

    <form method="get" class="buscador-form">
        <input type="text" name="busqueda" placeholder="Buscar por nombre o email" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit">Buscar</button>
    </form>

    <table class="usuarios-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['apellidos']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['teléfono']) ?></td>
                    <td><?= $row['fecha_registro'] ?></td>
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
