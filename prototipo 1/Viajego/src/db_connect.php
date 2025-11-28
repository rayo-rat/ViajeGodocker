<?php

// Función para establecer y devolver una conexión PDO a la base de datos especificada
function connectDatabase($db_name) {
    $host = 'db';              // Nombre del servicio Docker
    $user = 'user_docker';
    $pass = 'password_segura';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
         $pdo = new PDO($dsn, $user, $pass, $options);
         return $pdo;
    } catch (\PDOException $e) {
         // Retornamos null y mostramos el error de manera controlada en el front-end
         echo '<div class="alert alert-danger">Error de conexión a la base de datos ' . htmlspecialchars($db_name) . ': ' . $e->getMessage() . '</div>';
         return null; 
    }
}

// Conexión específica para el microservicio de Usuarios
function connectUserDB() {
    return connectDatabase('viajego_usuarios');
}

// Conexiones para el inventario
function connectVuelosDB() {
    return connectDatabase('viajego_vuelos');
}

function connectHotelesDB() {
    return connectDatabase('viajego_hoteles');
}

?>