<?php

require_once '../config/config.php';

$db = new Database();
$con = $db->conectar();

if (!isset($_SESSION['user_cliente'])) {
    die('Error: Usuario no autenticado.');
}
$idCliente = $_SESSION['user_cliente'];

$sqlProd = $con->prepare("SELECT email FROM clientes WHERE id = ? AND estatus = 1");
$sqlProd->execute([$idCliente]);
$row_cliente = $sqlProd->fetch(PDO::FETCH_ASSOC);

$status = 'Pagado';
$fecha = date('Y-m-d H:i:s');
$email = $row_cliente['email'];
$totalBOB = isset($_POST['total']) ? $_POST['total'] : 0;
$idTransaccion = uniqid();

if (isset($_FILES['comprobante']) && is_uploaded_file($_FILES['comprobante']['tmp_name'])) {
    $comprobante = file_get_contents($_FILES['comprobante']['tmp_name']);
} else {
    die('Error: No se ha subido un comprobante.');
}

$comando = $con->prepare("INSERT INTO compra (fecha, status, email, id_cliente, totalBOB, id_transaccion, medio_pago, comprobante) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$comando->execute([$fecha, $status, $email, $idCliente, $totalBOB, $idTransaccion, 'Qr', $comprobante]);
$id = $con->lastInsertId();

$response = array('success' => false);

if ($id > 0) {
    $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
    if ($productos != null) {
        foreach ($productos as $clave => $cantidad) {
            $sqlProd = $con->prepare("SELECT id, nombre, precio, descuento FROM productos WHERE id = ? AND activo = 1");
            $sqlProd->execute([$clave]);
            $row_prod = $sqlProd->fetch(PDO::FETCH_ASSOC);
            $precio = $row_prod['precio'];
            $descuento = $row_prod['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $sql_insert = $con->prepare("INSERT INTO detalle_compra (id_compra, id_producto, nombre, precio, cantidad) VALUES (?, ?, ?, ?, ?)");
            if ($sql_insert->execute([$id, $row_prod['id'], $row_prod['nombre'], $precio_desc, $cantidad])) {
                restarStock($row_prod['id'], $cantidad, $con);
            }
        }
        require_once 'mailer.php';
        $asunto = "Detalles de su pedido";
        $cuerpo = '<h4>Gracias por su compra</h4>';
        $cuerpo .= '<p>El ID de su compra es <b>' . $idTransaccion . '</b></p>';
        $cuerpo .= '<p>Detalles de la compra:</p>';
        $cuerpo .= '<table border="1">';
        $cuerpo .= '<tr><th>Producto</th><th>Precio Unitario</th><th>Precio Descuento</th><th>Cantidad</th><th>Subtotal</th></tr>';
        foreach ($productos as $clave => $cantidad) {
            $sqlProd = $con->prepare("SELECT nombre, precio, descuento FROM productos WHERE id = ?");
            $sqlProd->execute([$clave]);
            $row_prod = $sqlProd->fetch(PDO::FETCH_ASSOC);
            $precio_unitario = $row_prod['precio'];
            $descuento = $row_prod['descuento'];
            $precio_desc = $precio_unitario - (($precio_unitario * $descuento) / 100);
            $subtotal = $cantidad * $precio_desc;
            $cuerpo .= '<tr>';
            $cuerpo .= '<td>' . $row_prod['nombre'] . '</td>';
            $cuerpo .= '<td>' . MONEDA . ' ' . $precio_unitario . '</td>';
            $cuerpo .= '<td>' . MONEDA . ' ' . $precio_desc . '</td>';
            $cuerpo .= '<td>' . $cantidad . '</td>';
            $cuerpo .= '<td>' . MONEDA . ' ' . $subtotal . '</td>';
            $cuerpo .= '</tr>';
        }
        $cuerpo .= '</table>';

        $mailer = new Mailer();
        $mailer->enviarEmail($email, $asunto, $cuerpo, $idTransaccion);

        unset($_SESSION['carrito']);

        $response['success'] = true;
        $response['id'] = $idTransaccion;
    }
}

echo json_encode($response);

function restarStock($id, $cantidad, $con)
{
    $sql = $con->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $sql->execute([$cantidad, $id]);
}
