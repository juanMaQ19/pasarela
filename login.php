<?php
require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();
$proceso = isset($_GET['pago']) ? 'pago' : 'login';

$errors = array();
if (!empty($_POST)) {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $proceso = $_POST['proceso'] ?? 'login';
    if (esNulo([$usuario, $password])) {
        $errors[] = "Debe llenar todos los campos";
    }
    if (count($errors) == 0) {
        $errors[] = login($usuario, $password, $con, $proceso);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a href="index.php" class="navbar-brand">
                    <strong>Tech Market</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarHeader">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link active">Catálogo</a>
                        </li>
                        
                    </ul>
                    <a href="checkout.php" class="btn btn-primary">
                        Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart ?></span>
                    </a>
                </div>
            </div>
        </nav>
    </header>


    <main class="form-login m-auto pt-4">
        <h2 style="text-align: center; margin-bottom: 1em;">Iniciar sesion</h2>
        <div class="container-logo">
            <div>
                <img src="img/logo.png" alt="Logo Wsu" class="logo">
            </div>
        </div>

        <?php mostrarMensajes($errors); ?>

        <form class="row g-3" action="login.php" method="post" autocomplete="off">

            <input type="hidden" name="proceso" id="proceso" value="<?php echo $proceso; ?>">

            <div class="form-floating">
                <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Usuario" required>
                <label for="usuario">Usuario</label>
            </div>

            <div class="form-floating">
                <input class="form-control" type="password" name="password" id="password" placeholder="Contraseña" required>
                <label for="password">Contraseña</label>
            </div>

            <div class="col-12">
                <a href="recupera.php">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>

            <hr>

            <div class="col-12">
                ¿No tiene cuenta? <a href="registro.php">Registrate aqui</a>
            </div>

        </form>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>