<?php
require_once '../app/models/usuario.php';
require_once '../app/models/publicacion.php'; // Cambiado a minúscula para coincidir con tu sistema de archivos

class UsuarioController
{
    private $usuarioModelo;
    private $recetaModelo;

    public function __construct()
    {
        // Usamos nombres claros para evitar confusiones
        $this->usuarioModelo = new Usuario();
        $this->recetaModelo = new Publicacion();
    }

    public function mostrarLogin()
    {
        require_once '../app/views/login.php';
    }

    public function validar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->usuarioModelo->buscarPorEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['foto'] = $user['foto_perfil'];
                header("Location: index.php?action=home");
                exit();
            } else {
                header("Location: index.php?action=login&error=1");
                exit();
            }
        }
    }

    public function mostrarRegistro()
    {
        require_once '../app/views/registro.php';
    }

    public function registrarUsuario() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $rol = $_POST['rol'];
        
        $foto = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
            $foto = "img/perfiles/" . time() . "_" . $_FILES['foto_perfil']['name'];
            move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto);
        }

        if ($this->usuarioModelo->crear($nombre, $email, $password, $rol, $foto)) {
            header("Location: index.php?action=login&msg=cuenta_creada");
            exit();
        }
    }
}

    public function editarPerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
            $nombre = $_POST['nombre'];
            $fotoNombre = null;

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ruta = "img/perfiles/" . time() . "_" . $_FILES['foto']['name'];
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
                    $fotoNombre = $ruta;
                }
            }

            if ($this->usuarioModelo->actualizarPerfil($id, $nombre, $fotoNombre)) {
                $_SESSION['nombre'] = $nombre;
                header("Location: index.php?action=perfil&res=updated");
                exit();
            }
        }
    }

    public function eliminarPerfilCompleto()
    {
        if (!isset($_SESSION['user_id'])) return;

        $id_usuario = $_SESSION['user_id'];

        // 1. Borrar foto de perfil física
        $usuario = $this->usuarioModelo->buscarPorId($id_usuario);
        if ($usuario && !empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])) {
            unlink($usuario['foto_perfil']);
        }

        // 2. Borrar fotos de recetas físicas
        $publicaciones = $this->recetaModelo->listarPorUsuario($id_usuario);
        foreach ($publicaciones as $receta) {
            if (!empty($receta['imagen_url']) && file_exists($receta['imagen_url'])) {
                unlink($receta['imagen_url']);
            }
        }

        // 3. Borrar de la BD
        if ($this->usuarioModelo->eliminarTodoLoRelacionado($id_usuario)) {
            session_destroy();
            header("Location: index.php?action=registro&mensaje=cuenta_eliminada");
            exit();
        }
    }
}
