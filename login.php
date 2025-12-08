<?php
// login.php (Simplificado sin hashing para proyecto escolar)

session_start();
include_once 'db_connect.php'; 

$error_message = '';

// Si el usuario ya está logueado, redirigir a admin.php
if (isset($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Contraseña en texto plano

    $dbconn = connect_main_db();
    
    if (!$dbconn) {
        $error_message = 'Error de conexión con el servidor de la base de datos.';
    } else {
        // (CAMBIO CLAVE) Ahora seleccionamos la columna 'password' en texto plano
        $query = "SELECT id, password, products_table_name FROM admin_users WHERE username = $1";
        pg_prepare($dbconn, "login_query", $query);
        $result = pg_execute($dbconn, "login_query", array($username));
        $user = pg_fetch_assoc($result);

        // (CAMBIO CLAVE) Comparamos la contraseña en texto plano
        if ($user && $password === $user['password']) { 
            // ¡Credenciales válidas! Crear la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['products_table_name'] = $user['products_table_name']; 
            
            pg_close($dbconn);
            header('Location: admin.php');
            exit;
        } else {
            $error_message = 'Usuario o contraseña incorrectos.';
        }
        pg_close($dbconn);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            text-align: center;
            color: #1a3b5d;
            margin-bottom: 25px;
        }
        .btn-login {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
        }
    </style>
</head>
<body class="admin-body">
    <div class="login-container">
        <h2>🔑 Acceso de Administrador</h2>
        
        <?php if ($error_message): ?>
            <div class="error-message" style="display: block; margin-bottom: 20px;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="btn btn-create btn-login">
                Iniciar Sesión
            </button>
        </form>
    </div>
</body>
</html>