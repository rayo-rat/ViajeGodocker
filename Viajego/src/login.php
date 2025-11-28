<?php
// ¡CLAVE! Siempre inicia la sesión al principio de cualquier página que la use
session_start(); 

require_once 'db_connect.php'; 

$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger" role="alert">Introduce tu email y contraseña.</div>';
    } else {
        
        try {
            $sql = "SELECT id, password_hash, nombre FROM usuarios WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $password_hash = $user['password_hash'];
                
                if (password_verify($password, $password_hash)) {
                    
                    // 1. INICIAR SESIÓN y GUARDAR DATOS DEL USUARIO
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nombre'];
                    
                    // 2. REDIRECCIÓN (el usuario no verá el mensaje de éxito)
                    header('Location: /dashboard.php'); 
                    exit();
                    
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Contraseña incorrecta.</div>';
                }
            } else {
                $message = '<div class="alert alert-danger" role="alert">Usuario no encontrado.</div>';
            }
            
        } catch (\PDOException $e) {
            $message = '<div class="alert alert-danger" role="alert">Error de conexión al servidor.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | ViajeGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4 fw-bold" style="color: var(--color-turquesa);">Iniciar Sesión</h2>
                        <?php echo $message; ?>

                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-custom w-100 mb-3">Iniciar Sesión</button>
                            
                            <p class="text-center text-secondary">
                                ¿No tienes una cuenta? <a href="/registro.php" style="color: var(--color-turquesa); text-decoration: none;">Regístrate aquí</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>