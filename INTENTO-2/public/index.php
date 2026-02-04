<?php
session_start();

require_once '../config/conexion.php';
require_once '../app/controllers/UsuarioController.php';
require_once '../app/controllers/RecetaController.php';

$usuarioCtrl = new UsuarioController();
$recetaCtrl = new RecetaController();

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Acciones que requieren estar logueado obligatoriamente
$acciones_protegidas = ['nueva_receta', 'perfil', 'favoritos', 'comentar', 'compartir'];

if (in_array($action, $acciones_protegidas) && !isset($_SESSION['user_id'])) {
    header("Location: index.php?action=registro");
    exit();
}

switch ($action) {
    // --- RUTAS DE USUARIO ---
    case 'login':
        $usuarioCtrl->mostrarLogin();
        break;
    case 'validar':
        $usuarioCtrl->validar();
        break;
    case 'registro':
        $usuarioCtrl->mostrarRegistro();
        break;
    case 'registrarUsuario':
        $usuarioCtrl->registrarUsuario();
        break;
    case 'logout':
        session_destroy();
        header("Location: index.php?action=home");
        exit();
        break;

    // --- RUTAS DE RECETAS ---
    case 'nueva_receta':
        $recetaCtrl->nueva_receta();
        break;
    case 'guardarReceta':
        $recetaCtrl->guardarReceta();
        break;

    case 'perfil':
    if(isset($_SESSION['user_id'])) {
        // Esta línea es la que falta para traer tus datos de la base de datos
        $publicaciones = $recetaCtrl->obtenerMisPublicaciones($_SESSION['user_id']); 
        include '../app/views/perfil.php';
    } else {
        header("Location: index.php?action=login");
    }
    break;

    // En public/index.php dentro del switch
    case 'like':
        if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
            $recetaCtrl->darLike($_SESSION['user_id'], $_GET['id']);
        }
        header("Location: " . $_SERVER['HTTP_REFERER']); // Regresa a la página donde estabas
        break;

    case 'comentar':
        $recetaCtrl->comentar();
        break;
    case 'compartir':
        if (isset($_GET['id'])) {
            $recetaCtrl->compartir($_GET['id']);
        }
        break;

    // Dentro del switch ($action) en index.php

    case 'eliminar_receta':
        if (isset($_GET['id'])) {
            $recetaCtrl->eliminar($_GET['id']);
        }
        break;

    case 'editar_receta':
        if (isset($_GET['id'])) {
            $recetaCtrl->mostrarFormularioEditar($_GET['id']);
        }
        break;

    case 'actualizar_receta':
        $recetaCtrl->actualizar();
        break;

    case 'eliminar_comentario':
        if (isset($_GET['id'])) {
            $recetaCtrl->eliminarComentario($_GET['id']);
        }
        break;

    case 'favoritos':
        if (isset($_SESSION['user_id'])) {
            // Llamamos a una función específica para traer solo lo que nos gusta
            $publicaciones = $recetaCtrl->obtenerFavoritos($_SESSION['user_id']);
            include '../app/views/favoritos.php';
        } else {
            header("Location: index.php?action=login");
        }
        break;

    case 'eliminar_cuenta':
        $usuarioCtrl->eliminarPerfilCompleto();
        break;

    // --- RUTA PRINCIPAL ---
    case 'home':
    default:
        $publicaciones = $recetaCtrl->obtenerPublicaciones();
        include '../app/views/home.php';
        break;
}
