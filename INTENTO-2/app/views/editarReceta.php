<?php
// Protección de seguridad: Si no hay sesión, redirigir al login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Compartir Receta - Recetario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="logo-container">
                <img src="img/logo.png" alt="Logo App" class="main-logo">
            </div>
            <nav class="nav-menu">
                <a href="index.php?action=home">🏠 Inicio</a>
            </nav>
            <div class="auth-section">
                <p>Hola, <strong><?php echo $_SESSION['nombre']; ?></strong></p>
                <a href="index.php?action=logout" class="btn btn-register">Cerrar Sesión</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="upload-card">
                <h2>✏️ Editar mi publicación</h2>
                <form action="index.php?action=actualizar_receta" method="POST">
                    <input type="hidden" name="id" value="<?php echo $receta['id']; ?>">

                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?php echo htmlspecialchars($receta['titulo']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="5" required><?php echo htmlspecialchars($receta['descripcion']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-login">Guardar Cambios</button>
                    <a href="index.php?action=perfil" class="btn btn-register">Cancelar</a>
                </form>
            </div>
        </main>
    </div>
</body>

</html>