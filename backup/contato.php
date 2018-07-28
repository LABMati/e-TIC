<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");

$userData = json_decode(file_get_contents('php://input'),true);
if(empty($userData['name']) || empty($userData['email']) || empty($userData['subject']) || empty($userData['message']) || ! filter_var($userData['email'],FILTER_VALIDATE_EMAIL)){
	die(json_encode(array('result' => false, 'body' =>'Campos em branco')));
}
	
$name = strip_tags(htmlspecialchars($userData['name']));
$email_address = strip_tags(htmlspecialchars($userData['email']));
$subject = strip_tags(htmlspecialchars($userData['subject']));
$message = strip_tags(htmlspecialchars($userData['message']));
if(empty($userData['phone']))
	$phone = "Não informado";
else
	$phone = strip_tags(htmlspecialchars($userData['phone']));
	
$to = 'etic@ifc-camboriu.edu.br';
$email_subject = "Contato e-TIC: $subject";
$email_body = "Contato externo:\n\nNome: $name\n\nEmail: $email_address\n\nTelefone: $phone\n\nMensagem:\n$message";
$headers = "From: $email_address\n";
$headers .= "Reply-To: $email_address";
try{
    $coisa = mail($to,$email_subject,$email_body, $headers);
}
catch(Exception $e){
	die(json_encode(array('result' => false, 'body' =>'Erro ao enviar o e-mail')));
}
die(json_encode(array('result' => true, 'body' =>'Enviado com sucesso')));