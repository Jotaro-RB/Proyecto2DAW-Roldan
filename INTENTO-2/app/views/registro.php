<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta - Recetario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .register-container { height: 100vh; display: flex; justify-content: center; align-items: center; background-color: #f4f7f6; }
        .register-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 450px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-register-submit { width: 100%; background: #2e7d32; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h2 style="text-align: center; margin-bottom: 20px;">Únete a la comunidad</h2>
            <form action="index.php?action=registrarUsuario" method="POST">
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="nombre" required placeholder="Ej. Alexander Roldan">
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" required placeholder="tu@email.com">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required placeholder="Mínimo 8 caracteres">
                </div>
                <div class="form-group">
                    <label>¿Quién eres?</label>
                    <select name="rol">
                        <option value="usuario">Solo quiero ver recetas (Usuario)</option>
                        <option value="chef">Soy profesional y quiero subir recetas (Chef)</option>
                    </select>
                </div>
                <button type="submit" class="btn-register-submit">Crear Cuenta</button>
            </form>
            <p style="text-align: center; margin-top: 15px; font-size: 0.9em;">
                ¿Ya tienes cuenta? <a href="index.php?action=login" style="color: #2e7d32;">Inicia sesión</a>
            </p>
        </div>
    </div>
</body>
</html>