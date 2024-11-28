<?php
require_once '../config/database.php';
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $con = $db->conectar();

    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol = $_POST['rol'];

    $sql = $con->prepare("INSERT INTO admin (usuario, password, activo, fecha_alta, idRol) VALUES (?, ?, ?, NOW(), ?)");
    $sql->execute([$usuario, $password, 1, $rol]);

    header('Location: index.php');
    exit;
}

$db = new Database();
$con = $db->conectar();
$stm = "SELECT id, nombre FROM roles";
$roles = $con->query($stm)->fetchAll(PDO::FETCH_ASSOC);

require_once '../header.php';
?>

<main>
    <div class="container">
        <h4>Nuevo Usuario</h4>
        <hr>
        <form method="post">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="">Seleccione un rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</main>

<?php include '../footer.php'; ?>
