<?php

function conectarBasePostgreSQL(): PDO
{
    static $conexion = null;

    if ($conexion instanceof PDO) {
        return $conexion;
    }

    $host = trim((string) getenv('SUPABASE_DB_HOST'));
    $puerto = filter_var(getenv('SUPABASE_DB_PORT') ?: '5432', FILTER_VALIDATE_INT);
    $baseDatos = trim((string) getenv('SUPABASE_DB_NAME'));
    $usuario = trim((string) getenv('SUPABASE_DB_USER'));
    $contrasena = (string) getenv('SUPABASE_DB_PASSWORD');

    if (
        $host === '' ||
        !preg_match('/^[A-Za-z0-9.-]+$/', $host) ||
        $puerto === false ||
        $puerto < 1 ||
        $puerto > 65535 ||
        $baseDatos === '' ||
        str_contains($baseDatos, ';') ||
        $usuario === '' ||
        str_contains($usuario, ';') ||
        $contrasena === ''
    ) {
        error_log('Falta una configuración válida para la conexión PostgreSQL.');
        throw new RuntimeException('Falta una configuración válida para la conexión PostgreSQL.');
    }

    $dsn = sprintf(
        'pgsql:host=%s;port=%d;dbname=%s;sslmode=require',
        $host,
        $puerto,
        $baseDatos
    );

    try {
        $conexion = new PDO($dsn, $usuario, $contrasena, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $error) {
        error_log('No se pudo conectar con PostgreSQL: ' . $error->getMessage());
        throw $error;
    }

    return $conexion;
}
