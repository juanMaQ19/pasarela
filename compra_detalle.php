<?php
require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';

$token_session = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if ($orden == null || $token == null || $token != $token_session) {
    header("Location: compras.php");
    exit;
}

$db = new Database();
$con = $db->conectar();


$sqlCompra = $con->prepare("SELECT id, id_transaccion, fecha, totalBOB, status FROM compra WHERE id_transaccion = ? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
$idCompra = $rowCompra['id'];

$fecha = new DateTime($rowCompra['fecha']);
$fecha = $fecha->format('d/m/Y H:i:s');

$sqlDetalle = $con->prepare("SELECT id, nombre, precio, cantidad FROM detalle_compra WHERE id_compra = ?");
$sqlDetalle->execute([$idCompra]);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <?php include 'menu.php'; ?>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Detalle de la compra</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha: </strong><?php echo $fecha; ?></p>
                            <p><strong>Orden: </strong><?php echo $rowCompra['id_transaccion']; ?></p>
                            <p><strong>Total: </strong><?php echo MONEDA . $rowCompra['totalBOB']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)) {
                                    $precio = $row['precio'];
                                    $cantidad = $row['cantidad'];
                                    $subtotal = $precio * $cantidad;
                                ?>
                                    <tr>
                                        <td> <?php echo $row['nombre']; ?></td>
                                        <td> <?php echo MONEDA . " " . $precio; ?></td>
                                        <td> <?php echo $cantidad; ?></td>
                                        <td> <?php echo MONEDA . " " . $subtotal; ?></td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Detalles del pedido</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Estado del pedido: </strong><?php echo isset($rowCompra['status']) ? $rowCompra['status'] : 'No disponible'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>