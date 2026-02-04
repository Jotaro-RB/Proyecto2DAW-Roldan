/**
 * Lógica Global de Recetario Pro
 */

// Función para mostrar/ocultar el menú de opciones (LOS TRES PUNTOS)
function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);
    if (!menu) return;

    // Cerramos cualquier otro menú abierto
    document.querySelectorAll('.options-menu').forEach(m => {
        if (m.id !== 'menu-' + id) {
            m.style.display = 'none';
        }
    });

    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

// Ocultar publicación localmente (LocalStorage)
function hidePost(id, currentUser = 'invitado') {
    const post = document.querySelector(`.post-card[data-id="${id}"]`);
    if (post) {
        const storageKey = 'hidden_posts_' + currentUser;
        let hiddenPosts = JSON.parse(localStorage.getItem(storageKey)) || [];
        
        if (!hiddenPosts.includes(id)) {
            hiddenPosts.push(id);
            localStorage.setItem(storageKey, JSON.stringify(hiddenPosts));
        }

        post.style.transition = "all 0.4s ease";
        post.style.opacity = "0";
        setTimeout(() => { post.style.display = "none"; }, 400);
    }
}

// Restaurar publicaciones ocultas
function restaurarPublicaciones(currentUser = 'invitado') {
    const storageKey = 'hidden_posts_' + currentUser;
    if (confirm("¿Quieres volver a ver todas las publicaciones ocultas?")) {
        localStorage.removeItem(storageKey);
        window.location.reload();
    }
}

// Inicialización de eventos al cargar el DOM
document.addEventListener("DOMContentLoaded", () => {
    // 1. Manejo del Buscador
    const searchInput = document.getElementById('main-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            const term = searchInput.value.toLowerCase();
            const posts = document.querySelectorAll('.post-card');

            posts.forEach(post => {
                const title = post.querySelector('.post-title').textContent.toLowerCase();
                const desc = post.querySelector('.post-description').textContent.toLowerCase();
                post.style.display = (title.includes(term) || desc.includes(term)) ? 'block' : 'none';
            });
        });
    }

    // 2. Cerrar menús al hacer clic fuera
    window.addEventListener('click', (event) => {
        if (!event.target.matches('.options-btn')) {
            document.querySelectorAll('.options-menu').forEach(m => m.style.display = 'none');
        }
    });
});

function cerrarModal() {
    const modal = document.getElementById('modalError');
    if (modal) {
        modal.style.display = 'none';
        // Limpiamos la URL para que no vuelva a salir al recargar
        window.history.replaceState({}, document.title, "index.php?action=login");
    }
}