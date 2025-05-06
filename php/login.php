<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

    <!-- Header / Navbar -->
    <header>
        <nav>
            <div class="menu">
                <img src="../img/logo.png" alt="Logo" class="logo">
                <a href="index.php">INICIO</a>
                <a href="#">AGENDA</a>
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

    <!-- Formulario de inicio de sesión -->
    <main>
        <div class="login-container">
            <h2>Inicio de sesión</h2>
            <form action="#" method="post">
                <label for="email">Correo</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
