<?php

class Conexao{

	protected $conexao;
	const SUPER = 0;
	const ADMIN = 1;
	const APRESENTADOR = 2;
	const NORMAL = 3;
	//const TIPO = MINUTE;
	//const TEMPO = 1

	function __construct(){
		$this->conexao = new PDO('mysql:host=localhost;dbname=etic2018;charset=utf8','etic','ifc#tic@753');
	}
	function auth($token, $admin){
		if($admin == 1) $query = ' AND usuario.idusuario = seguranca.idusuario AND usuario.idtipo = 1';
		else if($admin <= 2) $query = ' AND usuario.idusuario = seguranca.idusuario AND usuario.idtipo <= 2';
		else $query='';
		$verificaToken = $this->conexao->prepare("SELECT seguranca.* FROM seguranca, usuario WHERE token = :token AND expiracao >=CURRENT_TIMESTAMP".$query);
		$verificaToken->bindParam(':token', $token, PDO::PARAM_STR);
		$verificaToken->execute();

		$validaToken = $this->conexao->prepare('DELETE FROM seguranca WHERE expiracao <= CURRENT_TIMESTAMP');
		$validaToken->execute();

		if(!$verificaToken->fetch()){
			throw new Exception("Unauthorized;ERRO, Token inválido!", 401);
		}
		$atualizaToken = $this->conexao->prepare('UPDATE seguranca SET expiracao = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE token = :token');
		$atualizaToken->bindParam(':token', $token, PDO::PARAM_STR);
		$atualizaToken->execute();

		$verificaAdmin = $this->conexao->prepare('SELECT usuario.idusuario FROM seguranca, usuario WHERE seguranca.token = :token AND usuario.idusuario = seguranca.idusuario AND usuario.idtipo = 1');
		$verificaAdmin->bindParam(':token', $token, PDO::PARAM_STR);
		$verificaAdmin->execute();

		if($verificaAdmin->fetch()){
			return true;
		}
		return false;
	}
}