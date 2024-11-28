<?php
require_once '../config/database.php';
require_once '../config/config.php';

$db = new Database();
$con = $db->conectar();

$id = $_GET['id'];

$sql = $con->prepare("UPDATE admin SET activo = 0 WHERE id = ?");
$sql->execute([$id]);

header('Location: index.php');
exit;
