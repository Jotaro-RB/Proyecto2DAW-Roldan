<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Recetas</title>
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
                <a href="index.php?action=perfil">👤 Perfil</a>
                <a href="index.php?action=favoritos" class="active">⭐ Favoritos</a>
            </nav>
            <div class="auth-section">
                <?php if (isset($_SESSION['nombre'])): ?>
                    <div class="user-logged">
                        <p>Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>
                        <span class="badge-rol"><?php echo ucfirst($_SESSION['rol']); ?></span>
                        <a href="index.php?action=nueva_receta" class="btn btn-login" style="margin-top: 15px; display: block;">+ Publicar Plato</a>
                        <a href="index.php?action=logout" class="btn btn-register" style="margin-top: 10px;">Cerrar Sesión</a>
                    </div>
                <?php else: ?>
                    <p>¿Quieres compartir tu receta?</p>
                    <a href="index.php?action=login" class="btn btn-login">Iniciar Sesión</a>
                    <a href="index.php?action=registro" class="btn btn-register">Registrarse</a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="main-content">
            <header class="top-header">
                <h1>Descubre nuevas recetas</h1>
                <div class="search-bar">
    <input type="text" id="main-search" placeholder="Buscar platillos, ingredientes...">
</div>
            </header>
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
                            <article class="post-card" data-id="<?php echo $receta['id']; ?>">
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
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <button class="options-btn" onclick="toggleMenu(<?php echo $receta['id']; ?>)">•••</button>
                                            <div id="menu-<?php echo $receta['id']; ?>" class="options-menu">
                                                <?php if ($_SESSION['user_id'] == $receta['usuario_id']): ?>
                                                    <a href="index.php?action=editar_receta&id=<?php echo $receta['id']; ?>">✏️ Editar</a>
                                                    <a href="index.php?action=eliminar_receta&id=<?php echo $receta['id']; ?>" class="delete-link" onclick="return confirm('¿Borrar?')">🗑️ Eliminar</a>
                                                <?php else: ?>
                                                    <a href="javascript:void(0);" onclick="hidePost(<?php echo $receta['id']; ?>, USER_NAME)">🚫 No quiero ver esto</a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
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