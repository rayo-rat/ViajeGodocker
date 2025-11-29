<?php
require_once 'db_connect.php'; // Incluye el archivo de conexión

$message = ''; // Mensaje de éxito o error para el usuario
$nombre = '';
$apellido = '';
$email = '';
$password = '';

// 1. Procesa el formulario al enviarse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // 2. Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger" role="alert">El formato del email es inválido.</div>';
    } elseif (strlen($password) < 6) {
        $message = '<div class="alert alert-danger" role="alert">La contraseña debe tener al menos 6 caracteres.</div>';
    } else {
        // 3. Encriptar la contraseña de forma segura
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // 4. Prepara la consulta SQL (Previene inyecciones SQL)
        try {
            $pdo = connectUserDB(); // Usar la función de conexión refactorizada
            if ($pdo) {
                $sql = "INSERT INTO usuarios (nombre, apellido, email, password_hash) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                // 5. Ejecuta la consulta
                $stmt->execute([$nombre, $apellido, $email, $password_hash]);
                
                // Éxito:
                $message = '<div class="alert alert-success" role="alert">¡Registro exitoso! Ahora puedes <a href="/login.php" class="alert-link">iniciar sesión</a>.</div>';
                // Limpia los campos
                $nombre = $apellido = $email = ''; 
            } else {
                $message = '<div class="alert alert-danger" role="alert">Error de conexión a la base de datos de usuarios.</div>';
            }
            
        } catch (\PDOException $e) {
            // Error: Comprobar si es un error de clave duplicada (email ya registrado)
            if ($e->getCode() == 23000) { 
                $message = '<div class="alert alert-warning" role="alert">El email ya está registrado. Por favor, utiliza otro.</div>';
            } else {
                // Otro error de BD
                $message = '<div class="alert alert-danger" role="alert">Error al registrar: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | ViajeGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container form-card-container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card form-card-sophisticated">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4 fw-bold">Crear Cuenta</h2>
                        
                        <?php echo $message; ?>

                        <form method="POST" action="registro.php">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($nombre ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required value="<?php echo htmlspecialchars($apellido ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Mínimo 6 caracteres.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-custom w-100 mb-3">Registrarse</button>
                            
                            <p class="text-center text-secondary">
                                ¿Ya tienes una cuenta? <a href="/login.php">Iniciar Sesión</a>
                            </p>
                        </form>
                        
                        <div class="text-center">
                            <a href="/" class="back-to-home-link">
                                <i class="fas fa-arrow-left"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>