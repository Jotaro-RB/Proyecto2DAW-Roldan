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
                <h2>🍳 Compartir mi plato</h2>
                <form action="index.php?action=guardarReceta" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Título del plato</label>
                        <input type="text" name="titulo" required placeholder="Ej. Ceviche de Camarón">
                    </div>
                    <div class="form-group">
                        <label>Descripción / Preparación</label>
                        <textarea name="descripcion" rows="5" required placeholder="Escribe los pasos aquí..."></textarea>
                    </div>

                    <?php if($_SESSION['rol'] == 'chef'): ?>
                    <div class="form-group">
                        <label>Ingredientes (Solo para Chefs)</label>
                        <textarea name="ingredientes" placeholder="Lista de ingredientes..."></textarea>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Foto del resultado</label>
                        <input type="file" name="imagen" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-login">Publicar Receta</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>