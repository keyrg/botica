<?php 

// Datos de conexión para PostgreSQL en Render
define('DB_HOST', 'dpg-d1f2bmqli9vc739ggq80-a.oregon-postgres.render.com');
define('DB_NAME', 'botica');
define('DB_USER', 'botica_user');
define('DB_PASSWORD', '1T6NeWwgNoQDkKMxul7hLQ3eIHbquHoZ');
define('DB_PORT', '5432');
define('SGBD', 'pgsql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';sslmode=require');

// Datos para encriptación
define('METHOD', 'AES-256-CBC');
define('SECRET_KEY', '$#*ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijmnopqrstuvwxyz1234567890#*');
define('SECRET_IV', '20001109108103975194753');


