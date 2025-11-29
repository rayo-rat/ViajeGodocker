-- 1. CREACIÓN DE BASES DE DATOS SEPARADAS (ESQUEMAS)

CREATE DATABASE IF NOT EXISTS viajego_hoteles;
CREATE DATABASE IF NOT EXISTS viajego_vuelos;
CREATE DATABASE IF NOT EXISTS viajego_autobuses;

-- 'viajego_usuarios' ya fue creada por la variable MYSQL_DATABASE en docker-compose.yml

-- 2. TABLAS DEL MICROSERVICIO DE USUARIOS
USE viajego_usuarios;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TABLAS DEL MICROSERVICIO DE VUELOS
USE viajego_vuelos;

DROP TABLE IF EXISTS vuelos; 

CREATE TABLE vuelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_vuelo VARCHAR(10) NOT NULL UNIQUE,
    origen_iata VARCHAR(3) NOT NULL,
    destino_iata VARCHAR(3) NOT NULL,
    fecha_salida DATETIME NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    asientos_disponibles INT NOT NULL
);

-- INSERCIÓN DE DATOS DE PRUEBA (VUELOS)
INSERT INTO vuelos (codigo_vuelo, origen_iata, destino_iata, fecha_salida, precio, asientos_disponibles) VALUES
('GO101', 'MEX', 'CUN', '2026-03-10 10:00:00', 2500.50, 150),
('GO102', 'MTY', 'CUN', '2026-03-10 14:30:00', 2800.00, 80),
('GO205', 'JAL', 'LAX', '2026-04-05 08:00:00', 4500.99, 50),
('GO300', 'MEX', 'HUX', '2026-03-20 18:00:00', 1800.00, 100);

-- 4. TABLAS DEL MICROSERVICIO DE HOTELES (CORREGIDO)
USE viajego_hoteles;

DROP TABLE IF EXISTS hoteles; 

CREATE TABLE hoteles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    estrellas INT NOT NULL,
    descripcion TEXT,
    precio_noche DECIMAL(10, 2) NOT NULL,
    disponibilidad BOOLEAN NOT NULL DEFAULT TRUE
);

-- INSERCIÓN DE DATOS DE PRUEBA (HOTELES)
INSERT INTO hoteles (nombre, ciudad, estrellas, precio_noche) VALUES
('Grand Oasis Cancún', 'Cancún', 5, 3500.00),
('Hotel Riu Plaza Guadalajara', 'Guadalajara', 4, 1800.50),
('The Reef Marina Huatulco', 'Huatulco', 4, 2200.00),
('Hyatt Regency Ciudad de México', 'Ciudad de México', 5, 4100.00);

-- 5. OTRAS TABLAS INICIALES
USE viajego_autobuses;
CREATE TABLE IF NOT EXISTS rutas_autobus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origen VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL
);

-- 6. LÍNEAS DE CORRECCIÓN DE PERMISOS (CLAVE)
GRANT ALL PRIVILEGES ON viajego_vuelos.* TO 'user_docker'@'%';
GRANT ALL PRIVILEGES ON viajego_hoteles.* TO 'user_docker'@'%';
GRANT ALL PRIVILEGES ON viajego_autobuses.* TO 'user_docker'@'%';

FLUSH PRIVILEGES;