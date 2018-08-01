<?php

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

			if($checkinscrito)
			{
				$inscricoes = $this->conexao->prepare('DELETE FROM usuario_atividade WHERE idusuario = :idusuario and idatividade = :idatividade');
				$inscricoes->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
				$inscricoes->bindParam(':idatividade',$KEYS['idatividade'], PDO::PARAM_INT);
				$inscricoes->execute();
			}

			else{
				$vagasDisponiveis = $this->conexao->prepare('SELECT (atividade.capacidade - count(usuario_atividade.idusuario)) as vagas
				FROM usuario_atividade, atividade
				WHERE usuario_atividade.idatividade = :idatividade and atividade.idatividade = :idatividade'); 
				$vagasDisponiveis->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
				$vagasDisponiveis->execute();
				$vagasDisponiveis = $vagasDisponiveis->fetch(PDO::FETCH_ASSOC);
				if($vagasDisponiveis['vagas'] <= 0 ){
					throw new Exception('Conflict;Vagas Esgotadas',409);
				}
				////
				$atividade = $this->conexao->prepare('SELECT hora_inicio, hora_fim FROM atividade WHERE idatividade = :idatividade');
				$atividade->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
				$atividade->execute();
				$atividade = $atividade->fetch(PDO::FETCH_ASSOC);

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

				$valida_hora->bindParam(':hora_inicio',$atividade['hora_inicio'],
					PDO::PARAM_STR);
				$valida_hora->bindParam(':hora_fim',$atividade['hora_fim'], PDO::PARAM_STR);
				$valida_hora->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
				$valida_hora->execute();
				$valida_hora = $valida_hora->fetch(PDO::FETCH_ASSOC);
				if(!$valida_hora)
				{
					$resultado = $this->conexao->prepare('INSERT INTO usuario_atividade(idusuario,idatividade) VALUES (:idusuario, :idatividade)');
					$resultado->bindParam(':idusuario', $idusuario['idusuario'], PDO::PARAM_STR);
					$resultado->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_STR);
					$resultado->execute();
				}else{
					throw new Exception("Conflict;ERRO, Horários não compatíveis", 409);
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

		if($checkinscrito)
		{
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

	function recuperarSenha($KEYS){
		try{
			$resultado = $this->conexao->prepare('SELECT idusuario FROM usuario where email = :email');
			$resultado->bindParam(':email', $KEYS['email'], PDO::PARAM_STR);
			$resultado->execute();

			$resultado = $resultado->fetch(PDO::FETCH_ASSOC);

			
			if($resultado){
				$this->idusuario = $resultado['idusuario'];
				$token = MD5(rand(3000,10000));


				$insereToken = $this->conexao->prepare('INSERT into seguranca (token, expiracao, idusuario) values(:token, DATE_ADD(NOW(), INTERVAL 1 HOUR), :idusuario)');

				$insereToken->bindParam(':token', $token, PDO::PARAM_STR);
				$insereToken->bindParam(':idusuario', $this->idusuario, PDO::PARAM_STR);
				$insereToken->execute();
			}
			else{
				throw new Exception("Not Found;ERRO, Email não cadastrado!", 404);
			}

			require 'email/PHPMailerAutoload.php';
			$mail = new PHPMailer;
			
			// Enable verbose debug output

			$mail->isSMTP();

			$mail->SMTPDebug = 1;		// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
			$mail->SMTPAuth = true;		// Autenticação ativada
			$mail->SMTPSecure = 'tls';	// SSL REQUERIDO pelo GMail
			$mail->Host = 'smtp.gmail.com';	// SMTP utilizado
			$mail->Port = 587;  		//

			$mail->Username = 'etic';                 // SMTP username
			$mail->Password = 'Etic#2017';                      // SMTP password

			$remetente = iconv("UTF-8", "CP1252", 'e-TIC 2018 - Não Responda');
			$mail->setFrom('etic@ifc-camboriu.edu', $remetente);
			$mail->addAddress($KEYS['email']);     // Add a recipient $KEYS['email']

			$mail->isHTML(true);
			$titulo = iconv("UTF-8", "CP1252", 'Recuperação de Senha');
			$mail->Subject = iconv("UTF-8", "CP1252", 'Recuperação de Senha e-TIC 2018');
			$mail->Body    = iconv("UTF-8", "CP1252",
			"Caso não tenha solicitado uma alteração de senha ignore este e-mail
			Recuperação de Senha e-TIC 2018</h1>
			Foi solicitado a recuperação da senha no sistema e-TIC para essa conta. Clique no botão abaixo para alterar sua senha. (Atenção esse link é válido por 1 hora!)
			www.etic.ifc-camboriu.edu.br/etic-2018/client/home.html?token=.$token.");
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			$mail->send();
		}catch(Exception $e){
			throw $e;
		}
	}

	function alterarSenha($KEYS, $token){
		parent::auth($token, Conexao::NORMAL);
		try{
			$resultado = $this->conexao->prepare('UPDATE usuario, seguranca SET usuario.senha = :senha WHERE usuario.idusuario = seguranca.idusuario AND seguranca.token = :token');
			$resultado->bindParam(':senha', hash('sha256', $KEYS['senha']), PDO::PARAM_STR);
			$resultado->bindParam(':token', $token, PDO::PARAM_STR);
			
			if(!$resultado->execute()) throw new Exception("Internal Server Error;ERRO, Não foi possível alterar a senha!", 500);

		}catch(Exception $e){
			throw $e;
		}
	}

	function login($KEYS){
		try{
			$resultado = $this->conexao->prepare('SELECT idusuario,email,senha from usuario where email = :login and senha = :senha');
			$resultado->bindParam(':login', $KEYS['login'], PDO::PARAM_STR);
			$resultado->bindParam(':senha', hash('sha256', $KEYS['senha']), PDO::PARAM_STR);
			// $resultado->bindParam(':senha', $KEYS['senha'], PDO::PARAM_STR);
			$resultado->execute();

			$resultado = $resultado->fetch(PDO::FETCH_ASSOC);

			
			if($resultado){
				$this->idusuario = $resultado['idusuario'];
				$token = MD5(rand(3000,10000));


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

	function cadastro($KEYS){
		try{
			$verifica = $this->conexao->prepare('SELECT count(idusuario) as existe FROM usuario 
				WHERE email = :email || cpf = :cpf');
			$verifica->bindParam(':email', $KEYS['email'], PDO::PARAM_STR);
			$verifica->bindParam(':cpf', $KEYS['cpf'], PDO::PARAM_STR);
			$verifica->execute();
			$verifica = $verifica->fetch(PDO::FETCH_ASSOC);
			
			if($verifica['existe'] > 0){
				throw new Exception('Conflict;Usuário já cadastrado', 409);
			}
		}

		
		catch(Exception $e){
			throw $e;
		}
					
		try{
					

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
			require 'email/PHPMailerAutoload.php';
			$mail = new PHPMailer;
			
			// Enable verbose debug output

			$mail->isSMTP();

			$mail->SMTPDebug = 1;		// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
			$mail->SMTPAuth = true;		// Autenticação ativada
			$mail->SMTPSecure = 'tls';	// SSL REQUERIDO pelo GMail
			$mail->Host = 'smtp.gmail.com';	// SMTP utilizado
			$mail->Port = 587;  		//

			$mail->Username = 'etic';                 // SMTP username
			$mail->Password = 'Etic#2017';                      // SMTP password

			$remetente = iconv("UTF-8", "CP1252", 'e-TIC 2018 - Não Responda');
			$mail->setFrom('etic@ifc-camboriu.edu.br', $remetente);
			$mail->addAddress($KEYS['email']);     // Add a recipient $KEYS['email']

			$mail->isHTML(true);
			$titulo = iconv("UTF-8", "CP1252", "Bem vindo e-TIC 2018");
			$mail->Subject = iconv("UTF-8", "CP1252", "Inscrição e-TIC 2018");
			$mail->Body    = iconv("UTF-8", "CP1252",
			"<!DOCTYPE html>
			<html lang='en' xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
			<head>
				<meta charset='utf-8'> <!-- utf-8 works for most cases -->
				<meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
				<meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
				<meta name='x-apple-disable-message-reformatting'>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
				<title>Alteração de senha - e-TIC 2018</title> <!-- The title tag shows in email notifications, like Android 4.4. -->
			
				<!-- CSS Reset : BEGIN -->
				<style>
			
					html,
					body {
						margin: 0 auto !important;
						padding: 0 !important;
						height: 100% !important;
						width: 100% !important;
					}
			
					* {
						-ms-text-size-adjust: 100%;
						-webkit-text-size-adjust: 100%;
					}
			
					div[style*='margin: 16px 0'] {
						margin: 0 !important;
					}
			
					table,
					td {
						mso-table-lspace: 0pt !important;
						mso-table-rspace: 0pt !important;
					}
			
					table {
						border-spacing: 0 !important;
						border-collapse: collapse !important;
						table-layout: fixed !important;
						margin: 0 auto !important;
					}
					table table table {
						table-layout: auto;
					}
			
					/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
					a {
						text-decoration: none;
					}
			
					/* What it does: Uses a better rendering method when resizing images in IE. */
					img {
						-ms-interpolation-mode:bicubic;
					}
			
					/* What it does: A work-around for email clients meddling in triggered links. */
					*[x-apple-data-detectors],  /* iOS */
					.unstyle-auto-detected-links *,
					.aBn {
						border-bottom: 0 !important;
						cursor: default !important;
						color: inherit !important;
						text-decoration: none !important;
						font-size: inherit !important;
						font-family: inherit !important;
						font-weight: inherit !important;
						line-height: inherit !important;
					}
			
					/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
					.a6S {
					   display: none !important;
					   opacity: 0.01 !important;
				   }
				   /* If the above doesn't work, add a .g-img class to any image in question. */
				   img.g-img + div {
					   display: none !important;
				   }
			
					/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
					@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
						.email-container {
							min-width: 320px !important;
						}
					}
					/* iPhone 6, 6S, 7, 8, and X */
					@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
						.email-container {
							min-width: 375px !important;
						}
					}
					/* iPhone 6+, 7+, and 8+ */
					@media only screen and (min-device-width: 414px) {
						.email-container {
							min-width: 414px !important;
						}
					}
			
				</style>
			
				<style>
			
					.button-td,
					.button-a {
						transition: all 100ms ease-in;
					}
					.button-td-primary:hover,
					.button-a-primary:hover {
						background: #49ac58 !important;
						border-color: #49ac58 !important;
					}
			
					@media screen and (max-width: 600px) {
			
						.email-container {
							width: 100% !important;
							margin: auto !important;
						}
			
						.fluid {
							max-width: 100% !important;
							height: auto !important;
							margin-left: auto !important;
							margin-right: auto !important;
						}
			
						/* What it does: Forces table cells into full-width rows. */
						.stack-column,
						.stack-column-center {
							display: block !important;
							width: 100% !important;
							max-width: 100% !important;
							direction: ltr !important;
						}
						/* And center justify these ones. */
						.stack-column-center {
							text-align: center !important;
						}
			
						/* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
						.center-on-narrow {
							text-align: center !important;
							display: block !important;
							margin-left: auto !important;
							margin-right: auto !important;
							float: none !important;
						}
						table.center-on-narrow {
							display: inline-block !important;
						}
			
						/* What it does: Adjust typography on small screens to improve readability */
						.email-container p {
							font-size: 17px !important;
						}
					}
			
				</style>
			
			
			</head>
			
			<body width='100%' style='margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: transparent;'>    <center style='width: 100%; background-color: transparent;'>
			
					<!-- Visually Hidden Preheader Text : BEGIN -->
					<div style='display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;'>
						Caso não tenha solicitado uma alteração de senha ignore este e-mail
					</div>
					<!-- Visually Hidden Preheader Text : END -->
			
					<!-- Preview Text Spacing Hack : BEGIN -->
					<div style='display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;'>
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
					</div>
					<!-- Preview Text Spacing Hack : END -->
			
					<!-- Email Body : BEGIN -->
					<table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' width='600' style='margin: 0 auto;' class='email-container'>
						<!-- Email Header : BEGIN -->
						<tr>
							<td style='padding: 20px 0; text-align: center'>
								<img src='http://www.etic.ifc-camboriu.edu.br/2018/img/png/etic.png' width='200' height='50' alt='alt_text' border='0' style='height: auto; font-family: sans-serif; font-size: 15px; line-height: 15px;'>
							</td>
						</tr>
						<!-- Email Header : END -->
						
						<!-- 1 Column Text + Button : BEGIN -->
						<tr>
							<td style='background-color: #ffffff;'>
								<table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
									<tr>
										<td style='padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;'>
											<h1 style='margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;'>Confirmação de inscrição e-TIC 2018</h1>
											<p style='margin: 0 0 10px;'>Olá,
												Seu cadastro foi efetuado com sucesso! Agora você pode gerenciar sua participação em todos os eventos do IX e-TIC (Encontro de Tecnologia da Informação e Comunicação do IFC
												Camboriú) que ocorrerá de 27 à 31 de agosto.
												Seu número inscrição e código de indicação é:</p>
										</td>
									</tr>
									<tr>
										<td style='padding: 0 20px 20px;'>
											<!-- Button : BEGIN -->
											<table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' style='margin: auto;'>
												<tr>
													<td class='button-td button-td-primary' style='border-radius: 4px; background: #379846;'>
														<div class='button-a button-a-primary' style='background: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #555555; display: block; border-radius: 4px; border: 2px solid #379846;'>2018$id</div>
													</td>
												</tr>
											</table>
											<!-- Button : END -->
										</td>
									</tr>                        
									<tr>
										<td style='padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;'>
											<p style='margin: 0 0 10px;'>Este número pode ser fornecido a um amigo e ser utilizado no cadastro dele como um código de indicação. Convide e traga mais gente com você! Aqueles também que tiverem mais 
												indicações e estiverem presentes na cerimônia de encerramento (31/08 a partir das 19h) concorrerão a prêmios.
												Você pode convidar e compartilhar seu Código de Indicação para seus amigos
												Aqueles que tiverem o maior número de participação nos eventos (palestras, minicursos, competições) irão concorrer a diversos prêmios.
												Você tem inúmeras razões para participar e trazer muita gente contigo para o IX e-TIC.
											</p>
										</td>
									</tr>
									<tr>
										<td style='padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;'>
											<p style='margin: 0 0 10px;'>
													Aguardamos você, atenciosamente
													<br>
													<br>
													Comissão organizadora do IX e-TIC.
											</p>
										</td>
									</tr>
									<!-- Button : BEGIN -->
									<table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' style='margin: auto; width: 100%'>
										<tr style='display: flex; justify-content: space-evenly; width: 100%'>
											<td class='button-td button-td-primary' style='border-radius: 4px; background: #379846; width: 20%; display: flex; justify-content: center; align-items: center;'>
												<a target='_blank' href='http://etic.ifc-camboriu.edu.br/2018' class='button-a button-a-primary' style='background: #379846; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;'>Site</a>
											</td>
											<td class='button-td button-td-primary' style='border-radius: 4px; background: #379846; width: 20%; display: flex; justify-content: center; align-items: center;'>
												<a target='_blank' href='https://www.facebook.com/eticifc' class='button-a button-a-primary' style='background: #379846; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;'>Facebook</a>
											</td>
											<td class='button-td button-td-primary' style='border-radius: 4px; background: #379846; width: 20%; display: flex; justify-content: center; align-items: center;'>
												<div class='button-a button-a-primary' style='background: #379846; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;'>(47) 21040804</div>
											</td>
										</tr>
									</table>
									<!-- Button : END -->
								</table>
							</td>
						</tr>
				</center>
			</body>
			</html>
			"
			);
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			$mail->send();
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
		$dia=(strftime('%A, %d de %B de %Y', strtotime('today'))) ;

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