<?php
require_once "config/server.php";

try {
    // Usar los valores de server.php para la conexión
    $dsn = "pgsql:host=" . DB_SERVER . ";port=5432;dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<h2 style='color: green;'>✅ Conexión exitosa a PostgreSQL.</h2>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error de conexión:</h2> " . $e->getMessage();
}
?>
