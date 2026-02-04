<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Recetario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Tus estilos anteriores se mantienen igual... */
        .login-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f7f6;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2e7d32;
        }

        /* ESTILOS PARA EL MODAL */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 350px;
            border-top: 5px solid #2e7d32;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <img src="img/logo.png" alt="Logo" style="max-width: 150px; margin-bottom: 20px;">
            <h2>¡Hola de nuevo!</h2>
            <p style="color: #666; margin-bottom: 30px;">Ingresa tus credenciales para cocinar</p>

            <form action="index.php?action=validar" method="POST">
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" required placeholder="tu@email.com">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-login" style="width: 100%; cursor: pointer;">Entrar</button>
            </form>
            <p style="margin-top: 20px; font-size: 0.9em;">
                ¿No tienes cuenta? <a href="index.php?action=registro" style="color: #2e7d32; font-weight: 600;">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div id="modalError" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-icon">⚠️</div>
                <h2 style="margin-bottom: 10px;">Acceso Denegado</h2>
                <p style="color: #555;">La contraseña ingresada es incorrecta. Por favor, inténtalo de nuevo.</p>
                <button onclick="cerrarModal()" class="btn btn-login" style="margin-top: 20px; width: 100%; cursor: pointer;">Entendido</button>
            </div>
        </div>
    <?php endif; ?>

    <script src="js/script.js"></script>
    <script>
        function cerrarModal() {
            const modal = document.getElementById('modalError');
            if (modal) {
                modal.style.display = 'none';
                // Limpia la URL para que no salga el error al refrescar
                window.history.replaceState({}, document.title, "index.php?action=login");
            }
        }
    </script>
</body>

</html>