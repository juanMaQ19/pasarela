<?php
require_once '../config/database.php';
require_once '../config/config.php';

$db = new Database();
$con = $db->conectar();

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = $con->prepare("UPDATE admin SET usuario = ?, password = ?, idRol = ? WHERE id = ?");
        $sql->execute([$usuario, $password, $rol, $id]);
    } else {
        $sql = $con->prepare("UPDATE admin SET usuario = ?, idRol = ? WHERE id = ?");
        $sql->execute([$usuario, $rol, $id]);
    }

    header('Location: index.php');
    exit;
}

// Obtener la información del usuario
$sql = $con->prepare("SELECT * FROM admin WHERE id = ?");
$sql->execute([$id]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

// Obtener la lista de roles desde la base de datos
$sqlRoles = $con->prepare("SELECT id, nombre FROM roles");
$sqlRoles->execute();
$roles = $sqlRoles->fetchAll(PDO::FETCH_ASSOC);

require_once '../header.php';
?>

<main>
    <div class="container">
        <h4>Editar Usuario</h4>
        <hr>
        <form method="post">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario['usuario']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (dejar en blanco si no deseas cambiarla)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="">Seleccione un rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['id']; ?>" <?php echo ($rol['id'] == $usuario['idRol']) ? 'selected' : ''; ?>>
                            <?php echo $rol['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</main>

<?php include '../footer.php'; ?>
