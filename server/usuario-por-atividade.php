<<?php  

$db = new PDO('mysql:host=localhost;dbname=etic2018;charset=utf8','etic','ifc#tic@753');
 
$response = $db->prepare("SELECT FROM usuario_atividade")