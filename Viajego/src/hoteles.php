<?php
// Configuración para el servicio de HOTELES (BD separada)
$host = 'db';
$db   = 'viajego_hoteles'; // Usamos la base de datos de hoteles
$user = 'user_docker';
$pass = 'password_segura'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$hoteles = [];
$error_message = '';

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Consulta para obtener todos los hoteles
     $sql = "SELECT nombre, ciudad, estrellas, precio_noche, disponibilidad FROM hoteles";
     $stmt = $pdo->query($sql);
     $hoteles = $stmt->fetchAll();
     
} catch (\PDOException $e) {
     $error_message = "Error de conexión/consulta: " . $e->getMessage();
}

// Opcional: Iniciar sesión si quieres mostrar la barra de navegación completa
session_start();
// $is_logged_in = isset($_SESSION['user_id']);
// $user_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoteles Disponibles | ViajeGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container mt-5 pt-5">
        <h1 class="text-center fw-bold mb-4" style="color: var(--color-turquesa);">🛏️ Hoteles Disponibles</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php else: ?>
        
            <p class="text-secondary text-center mb-5">Mostrando <?php echo count($hoteles); ?> resultados encontrados.</p>

            <table class="table table-hover shadow-sm rounded overflow-hidden">
                <thead style="background-color: var(--color-gris-pizarra); color: white;">
                    <tr>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">CIUDAD</th>
                        <th scope="col">ESTRELLAS</th>
                        <th scope="col">DISPONIBILIDAD</th>
                        <th scope="col">PRECIO/NOCHE</th>
                        <th scope="col">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($hoteles) > 0): ?>
                        <?php foreach ($hoteles as $hotel): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($hotel['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($hotel['ciudad']); ?></td>
                                <td><?php echo str_repeat('⭐', $hotel['estrellas']); ?></td>
                                <td>
                                    <?php if ($hotel['disponibilidad']): ?>
                                        <span class="badge bg-success">Disponible</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Agotado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold" style="color: var(--color-coral);">
                                    $<?php echo number_format($hotel['precio_noche'], 2); ?>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary-custom">Reservar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron hoteles disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>