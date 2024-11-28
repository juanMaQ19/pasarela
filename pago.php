<?php

require_once 'config/config.php';
$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id=? AND activo=1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
} else {
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/allmin.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include 'menu.php' ?>
    <img id="loader" src="images/cargando.gif" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none; z-index: 9999;">
    <main>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <p class="title">Escanea este Qr</p>
                <span><b>Nota:</b> <i>El monto de la transferencia debe ser igual al monto indicado por el sistema.</i></span>
                <div class="qrImage">
                    <img src="#" alt="Qr Pago">
                </div>
                <p class="title">Sube tu comprobante</p>
                <input type="file" id="fileInput" accept="image/*,.pdf" onchange="handleFileSelect(event)" required>
                <button class="btnEnviar" id="enviarBtn" onclick="enviarComprobante()">Enviar</button>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-6 pagos">
                    <h4 style="text-align: center;">Detalles de pago</h4>
                    <button class="qr" onclick="subirQr()">Paga con QR <i class="fas fa-qrcode qr_icon"></i></button>
                    <p style="margin-top: .5em;"><b>Nota:</b> <i>Las entregas se realizan <b>solamente en Oficinas</b>. Puede recoger su producto en la siguiente dirección: Alto Obrajes, IV Centenario. Puede encontrar la ubicación <a href="https://maps.app.goo.gl/tomvhKK4kuhH4Y8HA" target="_blank">aquí</a> o en el siguiente mapa:</i></p>
                    <iframe class="mapa" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30601.875816374893!2d-68.15256002568354!3d-16.514256!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f20f434a785f7%3A0xa9540056437e611b!2s4to%20Centenario!5e0!3m2!1ses-419!2sbo!4v1721770500817!5m2!1ses-419!2sbo" style="max-width: 100%; height: 15em; border-radius: 2em; margin-top: .5em; margin-left: .5em;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($lista_carrito == null) {
                                    echo '<tr><td colspan="5" class="text-center"><b>Lista vacía</b></td></tr>';
                                } else {
                                    $total = 0;
                                    foreach ($lista_carrito as $producto) {
                                        $_id = $producto['id'];
                                        $nombre = $producto['nombre'];
                                        $precio = $producto['precio'];
                                        $cantidad = $producto['cantidad'];
                                        $descuento = $producto['descuento'];
                                        $precio_desc = $precio - (($precio * $descuento) / 100);
                                        $subtotal = $cantidad * $precio_desc;
                                        $total += $subtotal;
                                ?>
                                        <tr>
                                            <td><?php echo $nombre ?></td>
                                            <td>
                                                <div id="subtotal_<?php echo $_id ?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal, 2, '.', ''); ?></div>
                                            </td>
                                        </tr>
                                <?php }
                                } ?>
                                <tr>
                                    <td colspan="2">
                                        <p class="h3 text-end" id="total"><?php echo MONEDA . number_format($total, 2, '.', ''); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=AY5YL-hCri9BFPQrnDBmqah6dxYmxYBNEA4g32UAKwFdej4c4-kGvF8zkZ5_2MS9OH98H6z4ekAsZr5b&currency=USD"></script>
    <script>
        var total = <?php echo json_encode($total); ?>;
        var isUploading = false;

        function subirQr() {
            document.getElementById('myModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('myModal').style.display = "none";
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('filePreview');
                    if (preview) {
                        preview.innerHTML = '';
                        const img = new Image();
                        img.onload = function() {
                            preview.appendChild(img);
                        };
                        img.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function enviarComprobante() {
            if (isUploading) return;

            const fileInput = document.getElementById('fileInput');
            if (fileInput.files.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, suba un comprobante primero.'
                });
                return;
            }

            const file = fileInput.files[0];
            if (file.size > 2000000 || !file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El archivo debe ser una imagen y no superar los 2MB.'
                });
                return;
            }

            isUploading = true;
            document.getElementById('enviarBtn').disabled = true;

            Swal.fire({
                title: 'Cargando...',
                text: 'Subiendo comprobante, por favor espere.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            const formData = new FormData();
            formData.append('comprobante', file);
            formData.append('total', total);

            fetch('clases/captura.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                isUploading = false;
                document.getElementById('enviarBtn').disabled = false;

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Comprobante enviado exitosamente.'
                    }).then(() => {
                        window.open("completado.php?key=" + data.id, "_blank");
                        window.location.href = "index.php";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al enviar el comprobante: ' + data.message
                    });
                }
            }).catch(error => {
                isUploading = false;
                document.getElementById('enviarBtn').disabled = false;

                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al enviar el comprobante.'
                });
            });
        }

        window.onclick = function(event) {
            const modal = document.getElementById('myModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>
