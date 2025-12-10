<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include("../../connection/db.php");

$mensaje = "";

/* ============================================================
   MENSAJE DESPUÉS DE REDIRECCIÓN (POST → REDIRECT → GET)
============================================================ */
if (isset($_GET['exito']) && $_GET['exito'] == 1) {
    $mensaje = "Producto agregado correctamente.";
}

/* ============================================================
   INSERTAR NUEVO PRODUCTO
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['agregarProducto'])) {

    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $marca = trim($_POST['marca']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    /* --- Subida de imagen --- */
    $imagenRuta = "";
    if (!empty($_FILES['imagen']['name'])) {

        // Crear carpeta si no existe
        $directorio = "../../img/productos/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreArchivo = uniqid() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaDestino = $directorio . $nombreArchivo;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
            $imagenRuta = "img/productos/" . $nombreArchivo;
        }
    }

    $stmt = $conexion->prepare(
        "INSERT INTO productos (nombre, categoría, marca, precio, stock, imagen_url)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssdis", $nombre, $categoria, $marca, $precio, $stock, $imagenRuta);

    if ($stmt->execute()) {
        header("Location: productos.php?exito=1");
        exit;
    } else {
        $mensaje = "Error al agregar producto.";
    }

    $stmt->close();
}

/* ============================================================
   ACTUALIZAR PRODUCTO
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actualizar'])) {

    $id = $_POST['id_producto'];
    $nuevoPrecio = floatval($_POST['precio']);
    $nuevoStock = intval($_POST['stock']);

    $stmt = $conexion->prepare("UPDATE productos SET precio = ?, stock = ? WHERE id_producto = ?");
    $stmt->bind_param("dii", $nuevoPrecio, $nuevoStock, $id);
    $stmt->execute();
    $stmt->close();

    $mensaje = "Producto actualizado.";
}

/* ============================================================
   OBTENER CATEGORÍAS Y MARCAS
============================================================ */
$categorias = $conexion->query("SELECT DISTINCT categoría FROM productos ORDER BY categoría ASC");
$marcas = $conexion->query("SELECT DISTINCT marca, categoría FROM productos ORDER BY marca ASC");

/* ============================================================
   BUSCAR PRODUCTOS
============================================================ */
$busqueda = $_GET['busqueda'] ?? "";
$busqueda = $conexion->real_escape_string($busqueda);

$sql = "SELECT * FROM productos 
        WHERE nombre LIKE '%$busqueda%' 
        OR categoría LIKE '%$busqueda%' 
        ORDER BY id_producto ASC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../../css/admin/admin.css">
    <link rel="stylesheet" href="../../css/admin/productos.css">

    <!-- CSS para mensaje con fade-out -->
    <style>
        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            opacity: 1;
            transition: opacity 1s ease-out;
        }
        .alert.fade-out {
            opacity: 0;
        }
    </style>
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
        <div class="alert" id="mensajeAlert"><?= $mensaje ?></div>
    <?php endif; ?>

    <!-- ==================== BUSCADOR ===================== -->
    <form method="get" class="buscador-form">
        <input type="text" name="busqueda" placeholder="Buscar por nombre o categoría" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- ============================================================
         AGREGAR NUEVO PRODUCTO
    ============================================================= -->
    <h2 class="admin-title">Agregar nuevo producto</h2>

    <form method="post" enctype="multipart/form-data" class="add-form">

        <input type="text" name="nombre" placeholder="Nombre" required>

        <select name="categoria" id="categoriaSelect" required>
            <option value="">Seleccione categoría</option>
            <?php while ($cat = $categorias->fetch_assoc()): ?>
                <option value="<?= $cat['categoría'] ?>"><?= $cat['categoría'] ?></option>
            <?php endwhile; ?>
        </select>

        <select name="marca" id="marcaSelect" required disabled>
            <option value="">Seleccione marca</option>
            <?php 
            mysqli_data_seek($marcas, 0); 
            while ($m = $marcas->fetch_assoc()): 
            ?>
                <option value="<?= $m['marca'] ?>" data-cat="<?= $m['categoría'] ?>">
                    <?= $m['marca'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="precio" step="0.01" placeholder="Precio" required>
        <input type="number" name="stock" placeholder="Stock" required>

        <input type="file" name="imagen" accept="image/*" required>

        <button type="submit" name="agregarProducto" class="btn-agregar">Agregar Producto</button>
    </form>

    <!-- ==================== LISTADO DE PRODUCTOS ===================== -->
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
                    <td><img src="../../<?= $row['imagen_url'] ?>" class="preview"></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['categoría'] ?></td>
                    <td><?= $row['marca'] ?></td>

                    <td><input type="number" step="0.01" name="precio" value="<?= $row['precio'] ?>"></td>
                    <td><input type="number" name="stock" value="<?= $row['stock'] ?>"></td>

                    <td>
                        <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                        <button type="submit" name="actualizar" class="btn-actualizar">Actualizar</button>
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

<!-- FILTRAR MARCAS SEGÚN CATEGORÍA -->
<script>
document.getElementById("categoriaSelect").addEventListener("change", function () {
    let selectedCat = this.value;
    let marcaSelect = document.getElementById("marcaSelect");

    marcaSelect.disabled = false;

    [...marcaSelect.options].forEach(opt => {
        if (opt.value === "") return;
        opt.style.display = (opt.dataset.cat === selectedCat) ? "block" : "none";
    });

    marcaSelect.value = "";
});
</script>

<!-- AUTO-OCULTAR MENSAJE A LOS 5 SEGUNDOS -->
<script>
setTimeout(() => {
    let msg = document.getElementById("mensajeAlert");
    if (msg) {
        msg.classList.add("fade-out");
        setTimeout(() => msg.remove(), 1000);
    }
}, 5000);
</script>

</body>
</html>
