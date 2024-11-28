<?php
require_once '../config/database.php';
date_default_timezone_set('America/La_Paz');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['idCompra'])) {
    $db = new Database();
    $con = $db->conectar();

    $idTransaccion = $_POST['idCompra'];
    $sqlUpdate = $con->prepare("UPDATE compra SET status = 'Entregado' WHERE id_transaccion = ?");
    $sqlUpdate->execute([$idTransaccion]);

    if ($sqlUpdate->rowCount() > 0) {
        echo "Actualización guardada correctamente";
    } else {
        echo "Error: No se pudo actualizar el estado de envío";
    }
} else {
    echo "Error: Datos incorrectos recibidos";
}
?>

