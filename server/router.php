<?php 

require_once("paramFilter.php");
require_once("conexao.php");
require_once("usuario.php");
require_once("atividade.php");
require_once("reports.php");

$usuario = new Usuario();
$atividade = new Atividade();
$report = new Reports();

$_param = explode("?", $_SERVER['REQUEST_URI']);
$HEADER = [];
parse_str($_param[1], $HEADER); //=_$GET

$KEYS = [];
$KEYS = json_decode(file_get_contents("php://input"),true);
$option = $HEADER['option'];

try{
	switch ($option) {
		case 'nivelPermissao': die($usuario->nivelPermissao($HEADER['token']));
		case 'alterarSenha': die($usuario->alterarSenha($KEYS, $HEADER['token']));
		case 'recuperarSenha': die($usuario->recuperarSenha($KEYS));
		case 'cadastrarUsuario': die($usuario->cadastro($KEYS));
		case 'cadastrarApresentador': die($usuario->cadastrarApresentador($KEYS, $HEADER['token']));
		case 'login': die($usuario->login($KEYS));
		case 'carregarApresentadores': die($usuario->carregarApresentadores($HEADER['token']));
		case 'inscricao': die($usuario->inscricao($KEYS, $HEADER['token']));
		case 'inscricaoForcada': die($usuario->inscricaoForcada($KEYS, $HEADER['token']));
		case 'carregarAtividadesApresentador': die($atividade->carregarAtividadesApresentador($HEADER['token']));
		case 'carregarChamada': die($atividade->carregarChamada($KEYS, $HEADER['token']));
		case 'fazerChamada': die($atividade->fazerChamada($KEYS, $HEADER['token']));
		case 'carregarUsuarios': die($usuario->carregar($HEADER['token']));
		case 'alterarAutorizacao': die($usuario->alterarAutorizacao($KEYS, $HEADER['token']));
		case 'desinscricao': die($usuario->desinscricao($KEYS, $HEADER['token']));
		case 'cadastrarAtividade': die($atividade->cadastro($KEYS, $HEADER['token']));
		case 'carregarAtividades': die($atividade->carregar($HEADER['token']));
		case 'carregarForcado': die($atividade->carregarForcado($KEYS, $HEADER['token']));
		case 'gerarCertificado': die($usuario->gerarCertificado($HEADER['token']));
		case 'carregarCategorias': die($atividade->carregarCategorias($HEADER['token']));
		case 'excluirAtividade': die($atividade->excluir($KEYS, $HEADER['token']));
		case 'editarAtividade': die($atividade->editar($KEYS, $HEADER['token']));
		case 'report': die($report->authenticate($KEYS, $HEADER['token']));
		case 'carregarInscritos': die($atividade->carregarInscritos($GET['id']));
		case 'carregarAtividadeUnica': die($atividade->carregarAtividadeUnica($GET['id']));
		case 'carregarTodasAtividades': die($atividade->carregarTodasAtividades());
		default: throw new Exception("Not Found;ERRO, Metodo não encontrado", 404);
	}	
} catch (Exception $e) {
	$erro = explode(";beleza", $e->getMessage());
	header("HTTP/1.1 ". $e->getCode()." ".$erro[0] );
} 



