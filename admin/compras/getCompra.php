<?php
require_once '../config/database.php';
require_once '../config/config.php';

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'ayudante')) {
    header("Location: ../index.php");
    exit;
}


$orden = $_POST['orden'] ?? null;


if ($orden == null) {
    exit;
}

$db = new Database();
$con = $db->conectar();

$sqlCompra = $con->prepare("SELECT compra.id, compra.id_transaccion, compra.fecha, compra.totalBOB, CONCAT(clientes.nombres, ' ', clientes.apellidos) AS cliente_nombre,
    clientes.telefono, clientes.email, compra.comprobante
    FROM compra 
    INNER JOIN clientes ON compra.id_cliente = clientes.id
    WHERE id_transaccion = ? LIMIT 1");

$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);

if (!$rowCompra) {
    exit;
}

$idCompra = $rowCompra['id'];

$fecha = new DateTime($rowCompra['fecha']);
$fecha = $fecha->format('d/m/Y H:i:s');

$comprobante = $rowCompra['comprobante'];

$comprobanteBase64 = base64_encode($comprobante);

$sqlDetalle = $con->prepare("SELECT id, nombre, precio, cantidad FROM detalle_compra WHERE id_compra = ?");
$sqlDetalle->execute([$idCompra]);

$html = '<p> <strong>Fecha: </strong> ' . $fecha . ' </p>';
$html .= '<p> <strong>ID compra: </strong> ' . $rowCompra['id_transaccion'] . ' </p>';
$html .= '<p> <strong>Total: </strong> ' . number_format($rowCompra['totalBOB'], 2, ',', '') . ' </p>';
$html .= '<p><strong> Datos del cliente: </strong>' . $rowCompra['cliente_nombre'] . '</p>';
$html .= '<p><strong> Contacto del cliente: </strong>' . $rowCompra['telefono'] .  ' - ' . $rowCompra['email'] . '</p>';

// Incluir el comprobante como una imagen
$html .= '<p><strong>Comprobante:</strong></p>';
$html .= '<div style="display: flex; justify-content: center;">';
$html .= '<img src="data:image/jpeg;base64,' . $comprobanteBase64 . '" alt="Comprobante" style="width: 20em; height: 30em;">';
$html .= '</div>';

$html .= '<table class="table">
<thead>
<tr>
<th>Producto</th>
<th>Precio</th>
<th>Cantidad</th>
<th>Subtotal</th>
</tr>
</thead>';

$html .= '<tbody>';
while ($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)) {
    $precio = $row['precio'];
    $cantidad = $row['cantidad'];
    $subtotal = $precio * $cantidad;

    $html .= '<tr>';
    $html .= '<td>' . $row['nombre'] . '</td>';
    $html .= '<td>' . MONEDA . " " . $precio . '</td>';
    $html .= '<td>' . $cantidad . '</td>';
    $html .= '<td>' . MONEDA . " " . $subtotal . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

echo json_encode($html, JSON_UNESCAPED_UNICODE);