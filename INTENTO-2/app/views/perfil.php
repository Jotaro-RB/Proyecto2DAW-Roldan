<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Recetario Pro</title>
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
                <a href="index.php?action=perfil" class="active">👤 Perfil</a>
                <a href="index.php?action=favoritos">⭐ Favoritos</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="javascript:void(0);" onclick="restaurarPublicaciones(USER_NAME)" id="btn-restaurar-nav">🔄 Mostrar ocultos</a>
                <?php endif; ?>
            </nav>

            <div class="auth-section">
                <p>Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>
                <span class="badge-rol"><?php echo ucfirst($_SESSION['rol']); ?></span>
                <a href="index.php?action=logout" class="btn btn-register" style="margin-top: 15px;">Cerrar Sesión</a>
            </div>
        </aside>
        <main class="main-content">
            <div class="profile-header" style="background: white; padding: 30px; border-radius: 15px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
    <div class="profile-avatar" style="width: 100px; height: 100px; background: #2e7d32; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; overflow: hidden; font-size: 2.5em; font-weight: 600;">
        <?php if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])): ?>
            <img src="<?php echo $usuario['foto_perfil']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
            <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
        <?php endif; ?>
    </div>

    <div class="profile-details">
        <h2 style="margin: 0; font-size: 1.8em;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
        <div class="profile-actions" style="margin-top: 10px; display: flex; gap: 15px;">
            <button onclick="document.getElementById('modalEditar').style.display='flex'" class="btn-edit" style="background: none; border: none; color: #2e7d32; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                ✏️ Editar Perfil
            </button>
            <a href="index.php?action=eliminar_cuenta" class="btn-delete" onclick="return confirm('¿Estás seguro? Se borrará todo tu historial de recetas y comentarios.')" style="text-decoration: none; color: #d32f2f; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                🗑️ Eliminar Cuenta
            </a>
        </div>
    </div>
</div>

            <div id="modalEditar" class="modal-overlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); justify-content: center; align-items: center; z-index: 1000;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 15px; width: 100%; max-width: 400px;">
        <h3>Editar Perfil</h3>
        <form action="index.php?action=actualizar_perfil" method="POST" enctype="multipart/form-data">
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Nombre Nuevo</label>
                <input type="text" name="nombre" value="<?php echo $_SESSION['nombre']; ?>" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Cambiar Foto</label>
                <input type="file" name="foto" accept="image/*" style="margin-top: 5px;">
            </div>
            <button type="submit" class="btn-login" style="width: 100%; background: #2e7d32; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">Guardar Cambios</button>
            <button type="button" onclick="document.getElementById('modalEditar').style.display='none'" style="width: 100%; background: none; border: none; color: #666; margin-top: 10px; cursor: pointer;">Cancelar</button>
        </form>
    </div>
</div>
            <section class="feed-container">
                <?php if (empty($publicaciones)): ?>
                    <div class="empty-state">
                        <div class="icon">🍳</div>
                        <h2>Aún no hay publicaciones</h2>
                        <p>Estamos preparando los mejores platillos para ti. ¡Vuelve pronto!</p>
                    </div>
                <?php else: ?>
                    <div class="feed-vertical">
                        <?php foreach ($publicaciones as $receta): ?>
                            <article class="post-card">
                                <header class="post-header">
                                    <div class="author-info">
                                        <div class="avatar-circle">
                                            <?php echo strtoupper(substr($receta['autor'], 0, 1)); ?>
                                        </div>
                                        <div class="author-text">
                                            <span class="author-name"><?php echo htmlspecialchars($receta['autor']); ?></span>
                                            <span class="author-badge">• <?php echo ucfirst($receta['rol']); ?></span>
                                        </div>
                                    </div>

                                    <div class="post-options-container">
                                        <button class="options-btn" onclick="toggleMenu(<?php echo $receta['id']; ?>)">•••</button>
                                        <div id="menu-<?php echo $receta['id']; ?>" class="options-menu">
                                            <?php if ($_SESSION['user_id'] == $receta['usuario_id']): ?>
                                                <a href="index.php?action=editar_receta&id=<?php echo $receta['id']; ?>">✏️ Editar</a>
                                                <a href="index.php?action=eliminar_receta&id=<?php echo $receta['id']; ?>" class="delete-link" onclick="return confirm('¿Seguro que quieres borrar este plato?')">🗑️ Eliminar</a>
                                            <?php else: ?>
                                                <a href="#" onclick="hidePost(<?php echo $receta['id']; ?>)">🚫 No quiero ver esto</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </header>

                                <div class="post-image">
                                    <img src="<?php echo htmlspecialchars($receta['imagen_url']); ?>" alt="Plato">
                                </div>

                                <footer class="post-footer">
                                    <div class="post-actions">
                                        <a href="index.php?action=like&id=<?php echo $receta['id']; ?>"
                                            class="action-btn <?php echo ($receta['usuario_dio_like']) ? 'liked' : ''; ?>"
                                            style="text-decoration:none;">❤️</a>

                                        <button class="action-btn" title="Comentar" onclick="document.getElementById('comment-<?php echo $receta['id']; ?>').focus()">💬</button>
                                        <a href="index.php?action=compartir&id=<?php echo $receta['id']; ?>" class="action-btn" style="text-decoration:none;">🔄</a>
                                    </div>

                                    <div class="post-content">
                                        <h3 class="post-title"><?php echo htmlspecialchars($receta['titulo']); ?></h3>
                                        <p class="post-description"><?php echo htmlspecialchars($receta['descripcion']); ?></p>
                                    </div>

                                    <div class="comments-list">
                                        <?php
                                        // Ahora los comentarios ya vienen dentro de la variable $receta
                                        if (!empty($receta['comentarios'])):
                                            foreach ($receta['comentarios'] as $com): ?>
                                                <div class="comment-item" style="display: flex; justify-content: space-between; align-items: center;">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($com['nombre']); ?>:</strong>
                                                        <span><?php echo htmlspecialchars($com['contenido']); ?></span>
                                                    </div>

                                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $com['usuario_id']): ?>
                                                        <div class="comment-actions">
                                                            <a href="index.php?action=eliminar_comentario&id=<?php echo $com['id']; ?>"
                                                                onclick="return confirm('¿Borrar comentario?')"
                                                                style="text-decoration:none; font-size: 0.8em;">🗑️</a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                        <?php endforeach;
                                        endif; ?>
                                    </div>

                                    <div class="comment-box">
                                        <form action="index.php?action=comentar" method="POST">
                                            <input type="hidden" name="publicacion_id" value="<?php echo $receta['id']; ?>">
                                            <input type="text" name="contenido" id="comment-<?php echo $receta['id']; ?>" placeholder="Escribe un comentario..." required>
                                        </form>
                                    </div>
                                </footer>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script src="js/script.js"></script>

    <script>
        const USER_NAME = "<?php echo $_SESSION['nombre'] ?? 'invitado'; ?>";

        document.addEventListener("DOMContentLoaded", () => {
            // Aplicar filtro de posts ocultos al cargar
            const storageKey = 'hidden_posts_' + USER_NAME;
            const hiddenPosts = JSON.parse(localStorage.getItem(storageKey)) || [];
            hiddenPosts.forEach(id => {
                const post = document.querySelector(`.post-card[data-id="${id}"]`);
                if (post) post.style.display = 'none';
            });
        });
    </script>
</body>

</body>

</html>