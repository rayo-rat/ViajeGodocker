<?php
// Inicia la sesión
session_start();

// Verifica si el usuario NO ha iniciado sesión (protección de ruta)
if (!isset($_SESSION['user_id'])) {
    // Si no hay sesión activa, redirige al login
    header('Location: /login.php');
    exit();
}

// Si la sesión existe, obtén el nombre del usuario
$user_name = htmlspecialchars($_SESSION['user_name']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ViajeGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="container mt-5 pt-5">
        <div class="alert alert-success text-center">
            <h1 class="display-4">¡Bienvenido al Panel de Control, <?php echo $user_name; ?>!</h1>
            <p class="lead">Tu sesión está activa y protegida.</p>
        </div>
        
        <div class="text-center mt-4">
            <a href="/logout.php" class="btn btn-danger btn-lg">Cerrar Sesión</a>
        </div>
    </div>
    
</body>
</html>