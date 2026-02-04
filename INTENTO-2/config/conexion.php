<?php
class Conexion
{
    public static function conectar()
    {
        $host = "localhost";
        $bd = "bd_recetas";
        $usuario = "root";
        $clave = "";

        try {
            // Configuramos la conexión con PDO
            $conn = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $usuario, $clave);

            // Configuramos PDO para que lance excepciones en caso de error
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            // En producción, es mejor guardar esto en un log y no mostrarlo al usuario
            die("Error crítico en la base de datos: " . $e->getMessage());
        }
    }
}
