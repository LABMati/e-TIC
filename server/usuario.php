<?php

header("Access-Control-Allow-Origin: http://etic.ifc-camboriu.edu.br");
header("Access-Control-Allow-Methods: *");

class Usuario extends Conexao{

	// function __construct($option){
	// 	parent::__contruct();
	// }


	protected $idusuario;

	function getUsuarios(){
		parent::auth($token, Conexao::NORMAL);
	}

	function inscricao($KEYS, $token){
		parent::auth($token, Conexao::NORMAL);
		try{
			
			// $vagasDisponiveis = $this->conexao->prepare('SELECT (atividade.capacidade - count(usuario_atividade.idusuario)) as vagas
			// 	FROM usuario_atividade, atividade
			// 	WHERE usuario_atividade.idatividade = :idatividade and atividade.idatividade = :idatividade'); 
			// $vagasDisponiveis->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
			// $vagasDisponiveis->execute();
			// $vagasDisponiveis = $vagasDisponiveis->fetch(PDO::FETCH_ASSOC);
			// if($vagasDisponiveis['vagas'] <= 0 ){
			// 	throw new Exception('Conflict;Vagas Esgotadas',409);
			// }
			// Esse de selecionar usuario é usado duas vezes NAOPRONTO
			$idusuario = $this->conexao->prepare('SELECT idusuario FROM seguranca WHERE token = :token');
			$idusuario->bindParam(':token', $token, PDO::PARAM_STR);
			$idusuario->execute();
			$idusuario = $idusuario->fetch(PDO::FETCH_ASSOC);

			$checkinscrito = $this->conexao->prepare('SELECT * FROM usuario_atividade WHERE idusuario = :idusuario and idatividade = :idatividade');
			$checkinscrito->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
			$checkinscrito->bindParam(':idatividade',$KEYS['idatividade'], PDO::PARAM_INT);
			$checkinscrito->execute();
			$checkinscrito = $checkinscrito->fetch(PDO::FETCH_ASSOC);

			if($checkinscrito){
				$inscricoes = $this->conexao->prepare('DELETE FROM usuario_atividade WHERE idusuario = :idusuario and idatividade = :idatividade');
				$inscricoes->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
				$inscricoes->bindParam(':idatividade',$KEYS['idatividade'], PDO::PARAM_INT);
				$inscricoes->execute();
			}
			else{
				$vagasDisponiveis = $this->conexao->prepare('SELECT (atividade.capacidade - count(usuario_atividade.idusuario)) as vagas from atividade INNER JOIN usuario_atividade ON atividade.idatividade = usuario_atividade.idatividade WHERE atividade.idatividade = :idatividade'); 
				$vagasDisponiveis->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
				$vagasDisponiveis->execute();
				$vagasDisponiveis = $vagasDisponiveis->fetch(PDO::FETCH_ASSOC);
				

				$atividade = $this->conexao->prepare('SELECT hora_inicio, hora_fim FROM atividade WHERE idatividade = :idatividade');
				$atividade->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
				$atividade->execute();
				$atividade = $atividade->fetch(PDO::FETCH_ASSOC);

				if($vagasDisponiveis['vagas'] > 0){
					$valida_hora = $this->conexao->prepare('SELECT u.idusuario FROM usuario u 
					INNER JOIN usuario_atividade ua
					ON u.idusuario = ua.idusuario
					INNER JOIN atividade a on a.idatividade = ua.idatividade
					WHERE
					(NOT
						(
							(:hora_inicio < a.hora_inicio and :hora_fim <= a.hora_inicio) 
							or
							(:hora_inicio >= a.hora_fim and :hora_fim > a.hora_fim)
						) 
					) and (u.idusuario = :idusuario)
					');

					$valida_hora->bindParam(':hora_inicio',$atividade['hora_inicio'], PDO::PARAM_STR);
					$valida_hora->bindParam(':hora_fim',$atividade['hora_fim'], PDO::PARAM_STR);
					$valida_hora->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
					$valida_hora->execute();
					$valida_hora = $valida_hora->fetch(PDO::FETCH_ASSOC);

					if(!$valida_hora){
						$resultado = $this->conexao->prepare('INSERT INTO usuario_atividade(idusuario,idatividade) VALUES (:idusuario, :idatividade)');
						$resultado->bindParam(':idusuario', $idusuario['idusuario'], PDO::PARAM_STR);
						$resultado->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
						$resultado->execute();
					}else{
						die (json_encode(['error'=> "409"]));
					}
				}else{
					$resultado = $this->conexao->prepare('INSERT INTO usuario_atividade(idusuario,idatividade) VALUES (:idusuario, :idatividade)');
					$resultado->bindParam(':idusuario', $idusuario['idusuario'], PDO::PARAM_INT);
					$resultado->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_INT);
					$resultado->execute();

					$tamFila = ($vagasDisponiveis['vagas']*-1);
					$fila = $this->conexao->prepare("SELECT u.idusuario FROM atividade a INNER JOIN usuario_atividade ua ON ua.idatividade=a.idatividade INNER JOIN usuario u ON u.idusuario=ua.idusuario WHERE ua.idatividade=:idAtividade ORDER BY(ua.hora_inscricao) DESC LIMIT :tamFila");
					$fila->bindParam(':idAtividade', $KEYS['idatividade'], PDO::PARAM_INT);
					$fila->bindParam(':tamFila', $tamFila, PDO::PARAM_INT);
					$fila->execute();
					$fila = $fila->fetchAll(PDO::FETCH_ASSOC);
					$fila = array_reverse($fila);
					for($i=0;$i<sizeof($fila);$i++){
						if($idusuario['idusuario']==$fila[$i]['idusuario'])
							$posFila=$i+1;
					}
					print_r($posFila);
				}
			}
		}
		catch(Exception $e){
			throw $e;
		}
	}

	function inscricaoForcada($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);

		$checkinscrito = $this->conexao->prepare('SELECT * FROM usuario_atividade WHERE idusuario = :idusuario and idatividade = :idatividade');
		$checkinscrito->bindParam(':idusuario',$KEYS['idusuario'], PDO::PARAM_INT);
		$checkinscrito->bindParam(':idatividade',$KEYS['idatividade'], PDO::PARAM_INT);
		$checkinscrito->execute();
		$checkinscrito = $checkinscrito->fetch(PDO::FETCH_ASSOC);

		if($checkinscrito){
			$inscricoes = $this->conexao->prepare('DELETE FROM usuario_atividade WHERE idusuario = :idusuario and idatividade = :idatividade');
			$inscricoes->bindParam(':idusuario',$KEYS['idusuario'], PDO::PARAM_INT);
			$inscricoes->bindParam(':idatividade',$KEYS['idatividade'], PDO::PARAM_INT);
			$inscricoes->execute();
		}else{
			$resultado = $this->conexao->prepare('INSERT INTO usuario_atividade(idusuario,idatividade) VALUES (:idusuario, :idatividade)');
			$resultado->bindParam(':idusuario', $KEYS['idusuario'], PDO::PARAM_STR);
			$resultado->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
			$resultado->execute();
		}

	}
	function carregar($token){
		parent::auth($token, Conexao::ADMIN);

		$usuarios = $this->conexao->prepare('SELECT idusuario, nome, email, cpf, idtipo FROM usuario order by idtipo');
		$usuarios->execute();
		$usuarios = $usuarios->fetchAll(PDO::FETCH_ASSOC);

		$permissoes = $this->conexao->prepare('SELECT * FROM tipo_usuario order by idtipo DESC' );
		$permissoes->execute();
		$permissoes = $permissoes->fetchAll(PDO::FETCH_ASSOC);

		$resposta = array(
			'usuarios' => $usuarios,
			'permissoes' => $permissoes
		);
		echo json_encode($resposta);

	}

	function alterarAutorizacao($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);

		try{
			$autorizacao = $this->conexao->prepare('UPDATE usuario, tipo_usuario SET usuario.idtipo = tipo_usuario.idtipo WHERE tipo_usuario.idtipo = :idtipo AND usuario.idusuario = :idusuario');
			$autorizacao->bindParam(':idtipo', $KEYS['idtipo'], PDO::PARAM_INT);
			$autorizacao->bindParam(':idusuario', $KEYS['idusuario'], PDO::PARAM_INT);
			$autorizacao->execute();

		}catch(Exception $e){
			throw $e;
		}

	}

	function fazerChamada($KEYS, $token){
		parent::auth($token, Conexao::APRESENTADOR);
	}

	function desinscricao($KEYS, $token){
		parent::auth($token, Conexao::NORMAL);

		// $idusuario = $this->conexao->prepare('SELECT idusuario FROM seguranca WHERE token = :token');
		// $idusuario->bindParam(':token', $token, PDO::PARAM_STR);
		// $idusuario->execute();
		// $idusuario = $idusuario->fetch(PDO::FETCH_ASSOC);

		// $inscricoes = $this->conexao->prepare('DELETE FROM usuario_atividade WHERE idusuario = :idusuario and idatividade = :idatividade');
		// $inscricoes->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
		// $inscricoes->bindParam(':idatividade',$KEYS['idatividade'], PDO::PARAM_INT);
		// $inscricoes->execute();
	}

	function carregarApresentadores($token){
		parent::auth($token, Conexao::NORMAL);

		try{
			$resultado = $this->conexao->prepare('SELECT usuario.idusuario, usuario.nome FROM usuario WHERE usuario.idtipo = 2');
			$resultado->execute();

			$resultado = $resultado->fetchAll(PDO::FETCH_ASSOC);

			$resultado = array(
				'apresentadores' => $resultado
			);
			echo json_encode($resultado);
		}catch(Exception $e){
			throw $e;
		}
	}

	function cadastrarApresentador($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);

		try{
			$resultado = $this->conexao->prepare('UPDATE usuario SET admin = 1 WHERE idusuario = :id');
			$resultado->bindParam(':id', $KEYS['id'], PDO::PARAM_STR);
			$resultado->execute();

			$resultado = $this->conexao->prepare('INSERT INTO apresentador(curriculo, idusuario) VALUES (:curriculo, :id)');
			$resultado->bindParam(':curriculo', $KEYS['curriculo'], PDO::PARAM_STR);
			$resultado->bindParam(':id', $KEYS['id'], PDO::PARAM_STR);
			$resultado->execute();			
		}catch(Exception $e){
			throw $e;
		}
	}

	function nivelPermissao($token){
		parent::auth($token, Conexao::NORMAL);
		try{
			$resultado = $this->conexao->prepare('SELECT usuario.idtipo FROM usuario, seguranca where seguranca.token = :token AND seguranca.idusuario = usuario.idusuario');

			$resultado->bindParam(':token', $token, PDO::PARAM_STR);
			$resultado->execute();

			$resultado = $resultado->fetch(PDO::FETCH_ASSOC);

			$resposta = array(
				'nivel' => $resultado['idtipo']
			);
			echo json_encode($resposta);

		}catch(Exception $e){
			throw $e;
		}
	}

	function rand_string( $length ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars),0,$length);
	}

	function recuperarSenha($KEYS){
		try{
			$resultado = $this->conexao->prepare('SELECT idusuario FROM usuario where email = :email');
			$resultado->bindParam(':email', $KEYS['email'], PDO::PARAM_STR);
			$resultado->execute();
			$resultado = $resultado->fetch(PDO::FETCH_ASSOC);
			if($resultado){
				$idusuario = $resultado['idusuario'];
				$pass = $this->rand_string(10);
				$hash = hash('sha256', $pass);
				$resultado = $this->conexao->prepare('UPDATE usuario SET senha = :senha WHERE idusuario = :id');
				$resultado->bindParam(':senha', $hash , PDO::PARAM_STR);
				$resultado->bindParam(':id', $idusuario, PDO::PARAM_STR);
				$resultado->execute();
				if($resultado->rowCount() > 0){
					$to = $KEYS['email'];
					$email_subject = "Nova senha e-TIC 2018 - Não responda";
					$email_body = wordwrap("Caso não tenha solicitado uma alteração de senha ignore este e-mail\n
Recuperação de Senha e-TIC 2018\n\n
Foi solicitado a recuperação da senha no sistema e-TIC para essa conta. Sua nova senha é: ".$pass, 70);
					$header = "From: etic@ifc-camboriu.edu.br";
					try{
						mail($to,$email_subject,$email_body, $header);
					}
					catch(Exception $e){
						throw $e;
					}
				}else{
					throw new Exception("Internal Server Error", 500);
				}
			}
			else{
				throw new Exception("Not Found;ERRO, Email não cadastrado!", 404);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	function alterarSenha($token){
		parent::auth($token, Conexao::NORMAL);
		try{
			$idUser = $this->conexao->prepare('SELECT idusuario FROM seguranca WHERE token = :token');
			$idUser->bindParam(':token', $token, PDO::PARAM_STR);
			$idUser->execute();
			$idUser = $idUser->fetch(PDO::FETCH_ASSOC);

			if(isset($idUser['idusuario'])){	
				$pass = $this->rand_string(10);
				$hash = hash('sha256', $pass);
				$resultado = $this->conexao->prepare('UPDATE usuario SET senha = :senha WHERE idusuario = :id');
				$resultado->bindParam(':senha', $hash , PDO::PARAM_STR);
				$resultado->bindParam(':id', $idUser['idusuario'], PDO::PARAM_STR);
				$resultado->execute();

				if(!$resultado->execute()){
					throw new Exception("Internal Server Error;ERRO, Não foi possível alterar a senha!", 500);
				}else{
					$emailUser = $this->conexao->prepare('SELECT email FROM usuario WHERE idusuario = :idusuario');
					$emailUser->bindParam(':idusuario', $idUser['idusuario'], PDO::PARAM_STR);
					
					if($emailUser->execute()){
						$emailUser = $emailUser->fetch(PDO::FETCH_ASSOC);

						$to = $emailUser['email'];
						$email_subject = "Nova senha e-TIC 2018 - Não responda";
						$email_body = wordwrap("Caso não tenha solicitado uma alteração de senha ignore este e-mail\n
Recuperação de Senha e-TIC 2018\n\n
Foi solicitado a recuperação da senha no sistema e-TIC para essa conta. Sua nova senha é: ".$pass, 70);
						$header = "From: etic@ifc-camboriu.edu.br";
						try{
							mail($to,$email_subject,$email_body, $header);
						}
						catch(Exception $e){
							throw $e;
						}
					}else{
						throw new Exception("Internal Server", 500);
					}
				}
			}

		}catch(Exception $e){
			throw $e;
		}
	}

	function login($KEYS){
		try{
			$hash = hash('sha256', $KEYS['senha']);
			$resultado = $this->conexao->prepare('SELECT idusuario,email,senha from usuario where email = :login and senha = :senha');
			$resultado->bindParam(':login', $KEYS['login'], PDO::PARAM_STR);
			$resultado->bindParam(':senha', $hash, PDO::PARAM_STR);
			// $resultado->bindParam(':senha', $KEYS['senha'], PDO::PARAM_STR);
			$resultado->execute();

			$resultado = $resultado->fetch(PDO::FETCH_ASSOC);


			if($resultado){
				$this->idusuario = $resultado['idusuario'];
				$token = MD5(rand(0,1000000000000));

				$insereToken = $this->conexao->prepare('INSERT into seguranca (token, expiracao, idusuario) values(:token, DATE_ADD(NOW(), INTERVAL 5 MINUTE),:idusuario)');

				$insereToken->bindParam(':token', $token, PDO::PARAM_STR);
				$insereToken->bindParam(':idusuario', $this->idusuario, PDO::PARAM_STR);
				$insereToken->execute();
				$resposta = array(
					'token' => $token
				);
				echo json_encode($resposta);
			}
			else{
				throw new Exception("Unauthorized;ERRO, Usuário não existente ou senha incorreta", 401);
			}

		}catch(Exception $e){
			throw $e;
		}
	}
	function validaCPF($cpf) {
		$cpf = preg_replace("/[^0-9]/", "", $cpf);
		$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
		if (strlen($cpf) != 11) 
			return false;
		if ($cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || 
			$cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || 
			$cpf == '88888888888' || $cpf == '99999999999') {
			return false;
		}
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{$c} != $d) 
				return false;
		}
		return true;
	}

	function cadastro($KEYS){
		try{
			$verifica = $this->conexao->prepare('SELECT count(idusuario) as existe FROM usuario 
				WHERE email = :email || cpf = :cpf');
			$verifica->bindParam(':email', $KEYS['email'], PDO::PARAM_STR);
			$verifica->bindParam(':cpf', $KEYS['cpf'], PDO::PARAM_STR);
			$verifica->execute();
			$verifica = $verifica->fetch(PDO::FETCH_ASSOC);
			if($verifica['existe'] > 0)
				throw new Exception('Conflict;Usuário já cadastrado', 409);
		}
		catch(Exception $e){
			throw $e;
		}
	try{
		if(!$this->validaCPF($KEYS['cpf']))
			die("CPF");
		$resultado = $this->conexao->prepare('INSERT INTO usuario(nome,email,senha,cpf,idtipo, certificado,turma,empresa,deficiencia,indicacao,escola,nascimento) VALUES(:nome, :email, :senha, :cpf, 3, NULL,  :turma, :empresa, :deficiencia, :indicacao, :escola, :nascimento)');
		$resultado->bindParam(':nome', $KEYS['nome'], PDO::PARAM_STR);
		$resultado->bindParam(':email', $KEYS['email'], PDO::PARAM_STR);
		$resultado->bindParam(':senha', hash('sha256', $KEYS['senha']), PDO::PARAM_STR);
		$resultado->bindParam(':cpf', $KEYS['cpf'], PDO::PARAM_STR);
		$resultado->bindParam(':nascimento', $KEYS['nascimento'], PDO::PARAM_STR);
		$resultado->bindParam(':turma', $KEYS['turma'], PDO::PARAM_STR);
		$resultado->bindParam(':empresa', $KEYS['empresa'], PDO::PARAM_STR);
		$resultado->bindParam(':deficiencia', $KEYS['deficiencia'], PDO::PARAM_STR);
		$resultado->bindParam(':indicacao', $KEYS['indicacao'], PDO::PARAM_STR);
		$resultado->bindParam(':escola', $KEYS['escola'], PDO::PARAM_STR);
		$resultado->execute();

	}catch(Exception $e){
		throw new Exception("INTERNAL SERVER ERROR;ERRO, Cadastro não realizado", 500);
	}
	$id=$this->conexao->lastInsertId();
		$this->enviarEmail($KEYS,$id);
	}

	function enviarEmail($KEYS,$id){
		$to = $KEYS['email'];
		$email_subject = "Cadastro e-TIC 2018 - Não responda";
		$email_body = wordwrap("Olá, seu cadastro foi efetuado com sucesso!\nAgora você pode gerenciar sua participação em todos os eventos do IX e-TIC (Encontro de Tecnologia da Informação e Comunicação do IFC - Camboriú) que ocorrerá de 27 à 31 de agosto. \nSeu número inscrição e código de indicação é: 2018".$id."\n
Este número pode ser fornecido a um amigo e ser utilizado no cadastro dele como um código de indicação. Convide e traga mais gente com você!\nAqueles também que tiverem mais indicações e estiverem presentes na cerimônia de encerramento (31/08 a partir das 19h) concorrerão a prêmios.\n
Você pode convidar e compartilhar seu Código de Indicação para seus amigos.\n
Aqueles que tiverem o maior número de participação nos eventos (palestras, minicursos, competições) irão concorrer a diversos prêmios.\n
Você tem inúmeras razões para participar e trazer muita gente contigo para o IX e-TIC.\n\n
Parabéns você acaba de ganhar um cupom de desconto de 15%  na casa do Código e Alura para divulgar, válido na semana do evento utilize o código promocional: IX&TIC_CDC\n\n
Aguardamos você, atenciosamente,\nComissão organizadora do IX e-TIC.", 70);
		$header = "From: etic@ifc-camboriu.edu.br";
		try{
			mail($to,$email_subject,$email_body, $header);
		}
		catch(Exception $e){
			throw $e;
		}
	}
	function gerarCertificado($token){
		parent::auth($token, Conexao::NORMAL);
		try{
			$dadosUsuario = $this->conexao->prepare('SELECT seguranca.idusuario, usuario.nome, usuario.cpf, usuario.certificado FROM seguranca, usuario WHERE token = :token AND usuario.idusuario = seguranca.idusuario');
			$dadosUsuario->bindParam(':token', $token, PDO::PARAM_STR);
			$dadosUsuario->execute();
			$dadosUsuario = $dadosUsuario->fetch(PDO::FETCH_ASSOC);

			if($dadosUsuario){
				$horasAtividades = $this->conexao->prepare('SELECT ROUND(SUM(timestampdiff(MINUTE, atividade.hora_inicio, atividade.hora_fim))/60) AS horas
						FROM atividade, usuario_atividade
						WHERE usuario_atividade.idusuario = :idusuario
						AND usuario_atividade.idatividade = atividade.idatividade
						AND usuario_atividade.presenca');

				$horasAtividades->bindParam(':idusuario', $dadosUsuario['idusuario'], PDO::PARAM_STR);
				$horasAtividades->execute();
				$horasAtividades = $horasAtividades->fetch(PDO::FETCH_ASSOC)['horas'];
			}
		}catch(Exception $e){
			throw $e;
		}

		// echo "oi";
		// die();

		include("mpdf60/mpdf.php");

		setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		$dia=(strftime('%A, %d de %B de %Y', strtotime('today')));

		$html="

			<page size='A4'>

				<head>
					<meta charset='UTF-8'>
				</head>

				<body style='margin:0; padding: 0;'>
					<div style='background-color:rgb(49, 123, 58);text-align:center; line-height:1;border-radius:10;z-index:999'>
						<h1 style='color:black; font-size: 50px; padding-top: 3%;'>CERTIFICADO</h1>
					</div>

					<div style='position:absolute;top:18%;left:5%;width:30%,background-color:blue'>
						<img src='../client/imagens/ifc.jpg' width='100%'>
					</div>

					<div style='position:absolute;top:23%;right:-10%;'>
						<img src='../client/imagens/brasao.jpg' width='25%'>						
					</div>

					<div style='position:absolute;top:35%;right:5%;text-align:center;'>
						<span style='font-size:13px'>Republica Federativa do Brasil</span><br>
						<span style='font-size:13px'>Ministério da Educação</span>
					</div>

					<div style='position:absolute;top:42%; margin-right: 5%; font-size:20px'>
						Certificamos que <i style='font-weight:bold;text-transform:capitalize;'>{$dadosUsuario['nome']}</i> participou da 
						<i style='font-weight:bold;font-style:normal;text-transform:capitalize;'>Semana Interna de Prevenção de Acidentes do Trabalho </i><i style='font-weight:bold;font-style:normal;'>(SIPAT)</i>, realizada no Instituto Federal Catarinense – <i>Campus</i> Camboriú, nos dias 11 a 15 de junho de 2018, totalizando $horasAtividades horas. 
					</div>
				
					<div style='position:absolute;top:55%;right:5%'>
					  CAMBORIÚ, $dia
					</div>

					<div style='position:absolute;top:63%;left:5%;width:27%;margin:1%'>
						<img src='../client/imagens/assinatura1.jpg' width='100%' style='padding:0 10% 0 10%'>
						<span style='font-size:13px'>Professor Rogério Luis Kerber</span><br>
						<span style='font-size:13px'> Diretor-Geral do Instituto Federal Catarinense</span><br>
						<span style='font-size:13px'>Campus Camboriú</span>
					</div>
					<div style='position:absolute;top:58%;left:32%;width:35%;margin:1%'>
						<img src='../client/imagens/assinatura2.jpg' width='100%' style='padding:0 10% 0 10%'>
						<span style='font-size:13px'>Professor Paulo Fernando Kuss</span><br>
						<span style='font-size:13px'>Coordenador de Extensão do Instituto Federal Catarinense</span><br>
						<span style='font-size:13px'>Campus Camboriú</span>
					</div>
					<div style='position:absolute;top:60%;right:5%;width:27%;margin:1%'>
						<img src='../client/imagens/assinatura3.jpg' width='100%' style='padding:0 10% 0 10%'>
						<span style='font-size:13px'>Professora Monique Koerich Simas Ersching Coordenadora do Projeto de Extensão – SIPAT Coordenadora do curso Técnico de Segurança do Trabalho do Instituto Federal Catarinense – Campus Camboriú</span>
					</div>

					<div style='position:absolute;bottom:5%;left:5%'>
						<span> Registro: {$dadosUsuario['certificado']} </span>
					</div> 
					<div style='position:absolute;bottom:5%;left:33%'>
						<span>Coordenação de Estágio e Extensão – Instituto Federal Catarinense – Campus Camboriú.</span>
					</div> 
				 </body>
			</page>
		";

		$mpdf=new mPDF('UTF-8','A4-L'); 
		$mpdf->showImageErrors = true;


		 $mpdf->SetDisplayMode('fullpage');

		 $html2="

			<page size='A4'>

				<head>
					<meta charset='UTF-8'>
				</head>

				<body style='margin:0; padding: 0;width:100%;'>
					<center>
						<div style='width:1500px; height:1000px;display:flex;text-align:center;justify-contentent:center;'>
						<img src='../client/imagens/certificadopag2.jpg' style='width:100%; height:auto;'>	
					    </div>
					</center>
				 </body>
			</page>
		";

		 //$css = file_get_contents("css/estilo.css");
		 // $mpdf->WriteHTML($css,1);
		 $mpdf->WriteHTML($html);


		 $mpdf->AddPage($html2);
		 $mpdf->WriteHTML($html2);

		//  $mpdf->WriteHTML($ak); ak vai oq vc quer na outra pagina

		 $mpdf->Output();
		 $mpdf->exit();
		 exit;
	}
}