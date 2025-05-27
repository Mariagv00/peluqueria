<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>

    <!-- Navbar -->
    <header>
        <nav>
            <div class="menu">
                <img src="../img/logo.png" alt="Logo" class="logo">
                <a href="index.php">INICIO</a>
                <a href="agenda.php">AGENDA</a>
                <a href="#">TIENDA</a>
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

    <!-- Formulario de registro -->
    <main>
        <div class="register-container">
            <h2>Registro de usuario</h2>
            <form action="../connection/controller.php" method="post">
                <input type="hidden" name="accion" value="registro">

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" required>

                <label for="email">Correo</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" required>

                <button type="submit">Registrarse</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
    </footer>
    <script src="../javascript/index.js"></script>
</body>
</html>
