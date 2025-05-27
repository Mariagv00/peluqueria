<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citas</title>
    <link rel="stylesheet" href="../css/citas.css">
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
                <a href="citas.php">Mis citas</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
</header>

<main class="main-content">
    <div class="citas-container">
        <h2>Mis Citas</h2>
        <div id="lista-citas"></div>
    </div>
</main>

<footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
</footer>

<script src="../javascript/citas.js"></script>

</body>
</html>
