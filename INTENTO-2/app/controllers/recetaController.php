<?php
require_once '../app/models/Publicacion.php';

class RecetaController
{

    private $modelo;

    public function __construct()
    {
        $this->modelo = new Publicacion();
    }

    public function nueva_receta()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
        require_once '../app/views/nuevaReceta.php';
    }

    public function obtenerMisPublicaciones($usuario_id)
    {
        // 1. Pedimos las publicaciones filtradas por TU ID
        $publicaciones = $this->modelo->listarPorUsuario($usuario_id);

        // 2. IMPORTANTE: También debemos cargar los comentarios para cada una
        // para que no de el error de "Undefined variable" que vimos antes
        foreach ($publicaciones as &$receta) {
            $receta['comentarios'] = $this->modelo->obtenerComentarios($receta['id']);
            $receta['usuario_dio_like'] = $this->modelo->verificarLike($_SESSION['user_id'], $receta['id']);
        }

        return $publicaciones;
    }

    public function guardarReceta()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $usuario_id  = $_SESSION['user_id'];
            $titulo      = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : null;

            // Gestión de la Imagen
            $nombre_original = $_FILES['imagen']['name'];
            $ext = pathinfo($nombre_original, PATHINFO_EXTENSION);
            $nombre_final = time() . "." . $ext;

            // RUTA CORREGIDA: Como ya estamos en 'public', entramos directo a img/
            $ruta_destino = "img/recetas/" . $nombre_final;

            // Intentar mover el archivo
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {

                $resultado = $this->modelo->guardar(
                    $usuario_id,
                    $titulo,
                    $descripcion,
                    $ingredientes,
                    $ruta_destino
                );

                if ($resultado) {
                    header("Location: index.php?action=home");
                    exit();
                } else {
                    echo "Error al guardar en la base de datos.";
                }
            } else {
                echo "Error al subir la imagen. Verifica que la carpeta 'public/img/recetas' tenga permisos de escritura.";
            }
        }
    }

    public function darLike($user_id, $post_id)
    {
        $this->modelo->toggleLike($user_id, $post_id);
    }

    // Procesar nuevo comentario
    public function comentar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $usuario_id = $_SESSION['user_id'];
            $publicacion_id = $_POST['publicacion_id'];
            $contenido = $_POST['contenido'];

            $this->modelo->guardarComentario($usuario_id, $publicacion_id, $contenido);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    // Lógica para compartir (Crear una copia de la receta para el usuario actual)
    public function compartir($id_receta)
    {
        if (isset($_SESSION['user_id'])) {
            $this->modelo->duplicarReceta($_SESSION['user_id'], $id_receta);
            header("Location: index.php?action=perfil"); // Te lleva a tu perfil para verla
            exit();
        }
    }

    public function obtenerPublicaciones()
    {
        $publicaciones = $this->modelo->listarTodas(); // Tu función actual

        // Por cada receta, le adjuntamos sus comentarios
        foreach ($publicaciones as &$receta) {
            $receta['comentarios'] = $this->modelo->obtenerComentarios($receta['id']);
            // También aprovechamos para adjuntar si el usuario actual le dio like
            if (isset($_SESSION['user_id'])) {
                $receta['usuario_dio_like'] = $this->modelo->verificarLike($_SESSION['user_id'], $receta['id']);
            } else {
                $receta['usuario_dio_like'] = false;
            }
        }

        return $publicaciones;
    }

    // Función para eliminar
    public function eliminar($id)
    {
        if (isset($_SESSION['user_id'])) {
            // El modelo se encarga de verificar que la receta sea del usuario logueado
            $resultado = $this->modelo->eliminar($id, $_SESSION['user_id']);

            if ($resultado) {
                header("Location: " . $_SERVER['HTTP_REFERER']); // Regresa a donde estaba (Home o Perfil)
                exit();
            }
        }
    }

    // Función para mostrar la vista de edición
    public function mostrarFormularioEditar($id)
    {
        $receta = $this->modelo->obtenerPorId($id);
        // Solo permitimos editar si es el dueño
        if ($receta && $receta['usuario_id'] == $_SESSION['user_id']) {
            require_once '../app/views/editarReceta.php';
        } else {
            header("Location: index.php?action=home");
        }
    }

    // Función para procesar los cambios de la edición
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];

            $resultado = $this->modelo->actualizar($id, $titulo, $descripcion, $_SESSION['user_id']);

            if ($resultado) {
                header("Location: index.php?action=perfil");
                exit();
            }
        }
    }

    public function eliminarComentario($id)
    {
        if (isset($_SESSION['user_id'])) {
            $resultado = $this->modelo->borrarComentario($id, $_SESSION['user_id']);
            if ($resultado) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }

    public function obtenerFavoritos($usuario_id)
    {
        // Pedimos al modelo solo las publicaciones con "like" de este usuario
        $publicaciones = $this->modelo->listarFavoritos($usuario_id);

        foreach ($publicaciones as &$receta) {
            $receta['comentarios'] = $this->modelo->obtenerComentarios($receta['id']);
            $receta['usuario_dio_like'] = true; // Si está aquí, es porque dio like
        }
        return $publicaciones;
    }
}
