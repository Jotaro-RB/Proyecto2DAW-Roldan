<?php
require_once '../config/conexion.php';

class Publicacion
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function guardar($usuario_id, $titulo, $descripcion, $ingredientes, $imagen_url)
    {
        $sql = "INSERT INTO publicaciones (usuario_id, titulo, descripcion, ingredientes, imagen_url) 
                VALUES (:u_id, :titulo, :desc, :ing, :img)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':u_id', $usuario_id);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->bindParam(':ing', $ingredientes);
        $stmt->bindParam(':img', $imagen_url);

        return $stmt->execute();
    }

    public function listarTodas()
    {
        // Usamos un JOIN para saber quién es el autor de cada receta
        $sql = "SELECT p.*, u.nombre as autor, u.rol 
            FROM publicaciones p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorUsuario($usuario_id)
    {
        $sql = "SELECT p.*, u.nombre as autor, u.rol 
            FROM publicaciones p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.usuario_id = :u_id 
            ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['u_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // En app/models/Publicacion.php
    public function toggleLike($usuario_id, $publicacion_id)
    {
        // Verificar si ya existe el like
        $check = $this->db->prepare("SELECT id FROM likes WHERE usuario_id = :u AND publicacion_id = :p");
        $check->execute(['u' => $usuario_id, 'p' => $publicacion_id]);

        if ($check->fetch()) {
            // Si existe, lo quitamos
            $sql = "DELETE FROM likes WHERE usuario_id = :u AND publicacion_id = :p";
        } else {
            // Si no existe, lo ponemos
            $sql = "INSERT INTO likes (usuario_id, publicacion_id) VALUES (:u, :p)";
        }
        return $this->db->prepare($sql)->execute(['u' => $usuario_id, 'p' => $publicacion_id]);
    }

    public function guardarComentario($u_id, $p_id, $texto)
    {
        $sql = "INSERT INTO comentarios (usuario_id, publicacion_id, contenido) VALUES (:u, :p, :c)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['u' => $u_id, 'p' => $p_id, 'c' => $texto]);
    }

    public function duplicarReceta($nuevo_usuario_id, $id_original)
    {
        // 1. Buscamos la publicación original
        $sql_orig = "SELECT * FROM publicaciones WHERE id = :id";
        $stmt = $this->db->prepare($sql_orig);
        $stmt->execute(['id' => $id_original]);
        $orig = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$orig) return false;

        // 2. Creamos la copia de la publicación para el usuario que comparte
        $sql_insert = "INSERT INTO publicaciones (usuario_id, titulo, descripcion, ingredientes, imagen_url) 
                   VALUES (:u_id, :titulo, :desc, :ing, :img)";
        $stmt_insert = $this->db->prepare($sql_insert);
        $stmt_insert->execute([
            'u_id'   => $nuevo_usuario_id,
            'titulo' => "[Compartido] " . $orig['titulo'],
            'desc'   => $orig['descripcion'],
            'ing'    => $orig['ingredientes'],
            'img'    => $orig['imagen_url']
        ]);

        // Obtenemos el ID de la nueva publicación recién creada
        $nueva_pub_id = $this->db->lastInsertId();

        // 3. COPIAR COMENTARIOS: Buscamos los comentarios de la publicación original
        $sql_comentarios = "SELECT * FROM comentarios WHERE publicacion_id = :old_id";
        $stmt_com = $this->db->prepare($sql_comentarios);
        $stmt_com->execute(['old_id' => $id_original]);
        $comentarios_existentes = $stmt_com->fetchAll(PDO::FETCH_ASSOC);

        // 4. Insertamos cada comentario en la nueva publicación compartida
        foreach ($comentarios_existentes as $com) {
            $sql_copy_com = "INSERT INTO comentarios (usuario_id, publicacion_id, contenido, fecha) 
                         VALUES (:u, :p, :c, :f)";
            $this->db->prepare($sql_copy_com)->execute([
                'u' => $com['usuario_id'],
                'p' => $nueva_pub_id,
                'c' => $com['contenido'],
                'f' => $com['fecha']
            ]);
        }

        return true;
    }

    public function obtenerComentarios($publicacion_id)
    {
        // Unimos con la tabla usuarios para saber quién escribió el comentario
        $sql = "SELECT c.*, u.nombre 
            FROM comentarios c 
            INNER JOIN usuarios u ON c.usuario_id = u.id 
            WHERE c.publicacion_id = :p_id 
            ORDER BY c.fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['p_id' => $publicacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id, $usuario_id)
    {
        try {
            // Iniciamos una transacción para que se borre todo o nada
            $this->db->beginTransaction();

            // 1. Borramos los likes asociados a esta publicación
            $sqlLikes = "DELETE FROM likes WHERE publicacion_id = :id";
            $stmtLikes = $this->db->prepare($sqlLikes);
            $stmtLikes->execute(['id' => $id]);

            // 2. Borramos los comentarios asociados a esta publicación
            $sqlCom = "DELETE FROM comentarios WHERE publicacion_id = :id";
            $stmtCom = $this->db->prepare($sqlCom);
            $stmtCom->execute(['id' => $id]);

            // 3. Finalmente, borramos la publicación (solo si pertenece al usuario)
            $sqlPub = "DELETE FROM publicaciones WHERE id = :id AND usuario_id = :u_id";
            $stmtPub = $this->db->prepare($sqlPub);
            $stmtPub->execute(['id' => $id, 'u_id' => $usuario_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Si algo falla, deshacemos los cambios
            $this->db->rollBack();
            return false;
        }
    }

    // Para buscar una receta específica para editar
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM publicaciones WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Para guardar los cambios editados
    public function actualizar($id, $titulo, $descripcion, $usuario_id)
    {
        $sql = "UPDATE publicaciones SET titulo = :t, descripcion = :d 
            WHERE id = :id AND usuario_id = :u_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            't' => $titulo,
            'd' => $descripcion,
            'id' => $id,
            'u_id' => $usuario_id
        ]);
    }

    public function borrarComentario($id, $usuario_id)
    {
        $sql = "DELETE FROM comentarios WHERE id = :id AND usuario_id = :u_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'u_id' => $usuario_id
        ]);
    }

    public function verificarLike($u_id, $p_id)
    {
        $sql = "SELECT id FROM likes WHERE usuario_id = :u AND publicacion_id = :p";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['u' => $u_id, 'p' => $p_id]);
        return $stmt->fetch() ? true : false;
    }

    public function listarFavoritos($usuario_id)
    {
        $sql = "SELECT p.*, u.nombre as autor, u.rol 
            FROM publicaciones p 
            INNER JOIN likes l ON p.id = l.publicacion_id 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            WHERE l.usuario_id = :u_id 
            ORDER BY l.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['u_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
