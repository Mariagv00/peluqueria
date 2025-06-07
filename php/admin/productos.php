<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
include("../../connection/db.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_producto'])) {
    $id = $_POST['id_producto'];
    $nuevoPrecio = floatval($_POST['precio']);
    $nuevoStock = intval($_POST['stock']);

    if ($nuevoPrecio < 0 || $nuevoStock < 0) {
        $mensaje = "El precio y el stock no pueden ser negativos.";
    } else {
        $stmt = $conexion->prepare("UPDATE productos SET precio = ?, stock = ? WHERE id_producto = ?");
        $stmt->bind_param("dii", $nuevoPrecio, $nuevoStock, $id);
        $stmt->execute();
        $stmt->close();
        $mensaje = "Producto actualizado correctamente.";
    }
}

$busqueda = $_GET['busqueda'] ?? "";
$busqueda = $conexion->real_escape_string($busqueda);
$sql = "SELECT * FROM productos WHERE nombre LIKE '%$busqueda%' OR categoría LIKE '%$busqueda%' ORDER BY id_producto ASC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../../css/admin/admin.css">
    <link rel="stylesheet" href="../../css/admin/productos.css">
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
    <h1 class="admin-title">Gestión de Productos</h1>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="get" class="buscador-form">
        <input type="text" name="busqueda" placeholder="Buscar por nombre o categoría" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Editar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td><img src="../../<?= htmlspecialchars($row['imagen_url']) ?>" style="width: 60px;"></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['categoría']) ?></td>
                        <td><?= htmlspecialchars($row['marca']) ?></td>
                        <td>
                            <input type="number" step="0.01" name="precio" value="<?= $row['precio'] ?>" required min="0">
                        </td>
                        <td>
                            <input type="number" name="stock" value="<?= $row['stock'] ?>" required min="0">
                        </td>
                        <td>
                            <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                            <button type="submit" class="btn-actualizar">Actualizar</button>
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
