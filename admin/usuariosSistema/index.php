<?php
require_once '../config/database.php';
require_once '../config/config.php';

$db = new Database();
$con = $db->conectar();

$sql = "SELECT a.id, a.usuario, a.email, a.activo, a.fecha_alta, r.nombre as rol
        FROM admin as a
        INNER JOIN roles as r ON a.idRol = r.id";
$resultado = $con->query($sql);

require_once '../header.php';
?>

<main>
    <div class="container">
        <h4>Usuarios Administrativos</h4>
        <hr>
        <a href="save.php" class="btn btn-primary">Nuevo Usuario</a>
        <table id="usuarios" class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Activo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['usuario']; ?></td>
                        <td><?php echo $row['activo'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $row['rol']; ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../footer.php'; ?>
<script>
      $(document).ready(function($) {
    $('#usuarios').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        }
    });
});
</script>
