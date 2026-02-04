<?php
require_once '../config/conexion.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Agrega esto dentro de la clase Usuario en app/models/Usuario.php
    // Añade esto a tu clase Usuario
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza tu función crear para que acepte la foto si existe
    public function crear($nombre, $email, $password, $rol, $foto = null)
    {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, foto_perfil) VALUES (:n, :e, :p, :r, :f)";
        $stmt = $this->db->prepare($sql);
        $pass = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute(['n' => $nombre, 'e' => $email, 'p' => $pass, 'r' => $rol, 'f' => $foto]);
    }

    public function actualizarPerfil($id, $nombre, $foto = null)
    {
        if ($foto) {
            $sql = "UPDATE usuarios SET nombre = :nombre, foto_perfil = :foto WHERE id = :id";
            $params = ['nombre' => $nombre, 'foto' => $foto, 'id' => $id];
        } else {
            $sql = "UPDATE usuarios SET nombre = :nombre WHERE id = :id";
            $params = ['nombre' => $nombre, 'id' => $id];
        }
        return $this->db->prepare($sql)->execute($params);
    }

    public function eliminarCuenta($id)
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        return $this->db->prepare($sql)->execute(['id' => $id]);
    }

    public function eliminarTodoLoRelacionado($id)
    {
        try {
            // Empezamos una transacción para que si algo falla, no borre nada a medias
            $this->db->beginTransaction();

            // A. Borrar comentarios hechos POR el usuario
            $sql1 = "DELETE FROM comentarios WHERE usuario_id = :id";
            $this->db->prepare($sql1)->execute(['id' => $id]);

            // B. Borrar publicaciones del usuario
            // (Esto también debería borrar los comentarios de otros en esas publicaciones)
            $sql2 = "DELETE FROM publicaciones WHERE usuario_id = :id";
            $this->db->prepare($sql2)->execute(['id' => $id]);

            // C. Finalmente, borrar al usuario
            $sql3 = "DELETE FROM usuarios WHERE id = :id";
            $this->db->prepare($sql3)->execute(['id' => $id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
