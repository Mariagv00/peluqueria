<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/register.css">
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
                <a href="login.php">Iniciar Sesión</a>
                <a href="register.php">Registrarse</a>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="register-container">
        <h2>Registro de usuario</h2>

        <form action="../connection/controller.php" method="post" id="registerForm">
            <input type="hidden" name="accion" value="registro">

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" required>

            <label for="email">Correo</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <small id="passwordHelp" style="color: red; display:none;">
                La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.
            </small>

            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" required>

            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" required>

            <button type="submit">Registrarse</button>
        </form>
    </div>
</main>

<footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
</footer>

<script src="../javascript/index.js"></script>
<script src="../javascript/register.js"></script>
</body>
</html>
