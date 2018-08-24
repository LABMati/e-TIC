<?php

class Atividade extends Conexao{

	protected $idusuario;

	function carregar($token){
		parent::auth($token, Conexao::NORMAL);

		//PEGA OS ID USUARIO APARTIR DO TOKEN
		$idusuario = $this->conexao->prepare('SELECT idusuario FROM seguranca WHERE token = :token');
		$idusuario->bindParam(':token', $token, PDO::PARAM_STR);
		$idusuario->execute();
		$idusuario = $idusuario->fetch(PDO::FETCH_ASSOC);

		$inscricoes = $this->conexao->prepare('SELECT a.idatividade FROM usuario u 
				INNER JOIN usuario_atividade ua
				ON u.idusuario = ua.idusuario
				INNER JOIN atividade a on a.idatividade = ua.idatividade
				WHERE u.idusuario = :idusuario');
		$inscricoes->bindParam(':idusuario',$idusuario['idusuario'], PDO::PARAM_INT);
		$inscricoes->execute();
		$inscricoes = $inscricoes->fetchAll(PDO::FETCH_ASSOC);

		
		//PEGA ATIVIDIDADES CADASTRADAS
		$atividades = $this->conexao->prepare('SELECT atividade.idatividade, categoria.nome as categoria, titulo, descricao, hora_inicio, hora_fim,
				(SELECT atividade.capacidade - count(idusuario)
				FROM usuario_atividade
				WHERE usuario_atividade.idatividade = atividade.idatividade ) as vagasDisponiveis
			FROM atividade, categoria
			WHERE categoria.idcategoria = atividade.idcategoria ORDER BY hora_inicio ASC');
		$atividades->execute();
		$atividades = $atividades->fetchAll(PDO::FETCH_ASSOC);


		$vagasDisponiveis = $this->conexao->prepare('SELECT count(idusuario) as vagasDisponiveis
				FROM usuario_atividade
				WHERE usuario_atividade.idatividade = :idatividade');
		$vagasDisponiveis->bindParam(':idatividade',$idusuario['idusuario'], PDO::PARAM_INT);
		$vagasDisponiveis->execute();
		$vagasDisponiveis = $vagasDisponiveis->fetchAll(PDO::FETCH_ASSOC);

		// $categorias = $this->conexao->prepare('SELECT * FROM categoria');
		// $categorias->execute();
		// $categorias = $categorias->fetchAll(PDO::FETCH_ASSOC);

		$resultado = array(
			'atividades' => $atividades,
			'inscricoes' => $inscricoes
			// 'categorias' =>$categorias
		);

		echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
	}
	function carregarForcado($keys, $token){
		parent::auth($token, Conexao::ADMIN);

		
		$idusuario = $keys['idusuario'];
		
		$inscricoes = $this->conexao->prepare('SELECT a.idatividade FROM usuario u
				INNER JOIN usuario_atividade ua
				ON u.idusuario = ua.idusuario
				INNER JOIN atividade a on a.idatividade = ua.idatividade
				WHERE u.idusuario = :idusuario');
		$inscricoes->bindParam(':idusuario',$idusuario, PDO::PARAM_INT);
		$inscricoes->execute();
		$inscricoes = $inscricoes->fetchAll(PDO::FETCH_ASSOC);

		$resultado = array(
			'inscricoes' => $inscricoes
		);
		echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

		// $atividades = $this->conexao->prepare('SELECT atividade.idatividade, categoria.nome as categoria, titulo, descricao, hora_inicio, hora_fim,
		// 		(SELECT atividade.capacidade - count(idusuario)
		// 		FROM usuario_atividade
		// 		WHERE usuario_atividade.idatividade = atividade.idatividade ) as vagasDisponiveis
		// 	FROM atividade, categoria
		// 	WHERE categoria.idcategoria = atividade.idcategoria ORDER BY hora_inicio ASC');
		// $atividades->execute();
		// $atividades = $atividades->fetchAll(PDO::FETCH_ASSOC);


		// $vagasDisponiveis = $this->conexao->prepare('SELECT count(idusuario) as vagasDisponiveis
		// 		FROM usuario_atividade
		// 		WHERE usuario_atividade.idatividade = :idatividade');
		// $vagasDisponiveis->bindParam(':idatividade',$idusuario['idusuario'], PDO::PARAM_INT);
		// $vagasDisponiveis->execute();
		// $vagasDisponiveis = $vagasDisponiveis->fetchAll(PDO::FETCH_ASSOC);


		// $resultado = array(
		// 	'atividades' => $atividades,
		// 	'inscricoes' => $inscricoes
		// );

		// echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
	}
	function cadastro($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);

		//FALTA VALIDAR DADOS NAOPRONTO
		try{
			$cadastro = $this->conexao->prepare('INSERT INTO atividade(idcategoria, descricao, capacidade, hora_inicio, hora_fim, titulo/*, idusuario*/) Values(:idcategoria, :descricao, :capacidade, :hora_inicio, :hora_fim, :titulo/*, :idusuario*/)');
			$cadastro->bindParam(':idcategoria', param_filter($KEYS['idcategoria'],'int'), PDO::PARAM_STR);
			$cadastro->bindParam(':descricao', param_filter($KEYS['descricao'],'str'), PDO::PARAM_STR);
			$cadastro->bindParam(':capacidade', param_filter($KEYS['capacidade'],'int'), PDO::PARAM_INT);
			$cadastro->bindParam(':hora_inicio', param_filter($KEYS['hora_inicio'],'str'), PDO::PARAM_STR);
			$cadastro->bindParam(':hora_fim', param_filter($KEYS['hora_fim'],'str'), PDO::PARAM_STR);
			$cadastro->bindParam(':titulo', param_filter($KEYS['titulo'],'str'), PDO::PARAM_STR);
			//$cadastro->bindParam(':idusuario', $KEYS['idusuario'], PDO::PARAM_INT);
			$cadastro->execute();
		}catch(Exception $e){
			throw $e;
		}
	}

	function carregarAtividadesApresentador($token){
		$atividades;
		if(parent::auth($token, Conexao::APRESENTADOR)){
			$atividades = $this->conexao->prepare('SELECT atividade.idatividade, atividade.titulo FROM atividade ORDER BY atividade.titulo ASC');
		}
		else{
			$atividades = $this->conexao->prepare('SELECT atividade.idatividade, atividade.titulo FROM atividade, seguranca WHERE atividade.idusuario = seguranca.idusuario AND seguranca.token = :token ORDER BY atividade.titulo ASC');
			$atividades->bindParam(':token', $token, PDO::PARAM_STR);
		}
		try{
			$atividades->execute();
			$atividades = $atividades->fetchAll(PDO::FETCH_ASSOC);

			$resultado = array(
				'atividades' => $atividades
			);
			echo json_encode($resultado);
		}catch(Exception $e){
			throw $e;
		}
	}

	function fazerChamada($KEYS, $token){
		$chamada;
		if(parent::auth($token, Conexao::APRESENTADOR)){
			$chamada = $this->conexao->prepare('
					UPDATE atividade, seguranca, usuario, usuario_atividade
					SET usuario_atividade.presenca = !usuario_atividade.presenca
					WHERE usuario_atividade.idatividade = :idatividade
					AND usuario_atividade.idusuario = :idusuario
				');
		}
		else{
			$chamada = $this->conexao->prepare('
					UPDATE atividade, seguranca, usuario, usuario_atividade
					SET usuario_atividade.presenca = !usuario_atividade.presenca
					WHERE seguranca.token = :token
					AND atividade.idusuario = seguranca.idusuario
					AND atividade.idatividade = usuario_atividade.idatividade
					AND usuario_atividade.idatividade = :idatividade
					AND usuario_atividade.idusuario = :idusuario
				');
			$chamada->bindParam(':token', $token, PDO::PARAM_STR);
		}

		try{
			
			$chamada->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_INT);
			$chamada->bindParam(':idusuario', $KEYS['idusuario'], PDO::PARAM_INT);
			$chamada->execute();
		}catch(Exception $e){
			throw $e;
		}
	}		

	function carregarChamada($KEYS, $token){
		$chamada;
		if(parent::auth($token, Conexao::APRESENTADOR)){
			$chamada = $this->conexao->prepare('
				SELECT DISTINCT usuario.idusuario, usuario.nome, usuario.email, usuario_atividade.presenca
				FROM atividade, seguranca, usuario, usuario_atividade
				WHERE atividade.idatividade = usuario_atividade.idatividade
				AND usuario_atividade.idatividade = :idatividade
				AND usuario_atividade.idusuario = usuario.idusuario
				ORDER BY usuario.nome ASC');
		}
		else{
			$chamada = $this->conexao->prepare('
				SELECT usuario.idusuario, usuario.nome, usuario.email, usuario_atividade.presenca
				FROM atividade, seguranca, usuario, usuario_atividade
				WHERE seguranca.token = :token
				AND atividade.idusuario = seguranca.idusuario
				AND atividade.idatividade = usuario_atividade.idatividade
				AND usuario_atividade.idatividade = :idatividade
				AND usuario_atividade.idusuario = usuario.idusuario
				ORDER BY usuario.nome ASC');
			$chamada->bindParam(':token', $token, PDO::PARAM_STR);
		}
		try{
			
			$chamada->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_INT);
			$chamada->execute();
			$chamada = $chamada->fetchAll(PDO::FETCH_ASSOC);
			$resultado = array(
				'chamada' => $chamada
			);
			echo json_encode($resultado);
		}catch(Exception $e){
			throw $e;
		}
	}	

	function carregarCategorias($token){
		parent::auth($token, Conexao::ADMIN);
			
		//PEGA AS CATEGORIAS
		$categorias = $this->conexao->prepare('SELECT idcategoria,nome FROM categoria');
		$categorias->execute();
		$categorias = $categorias->fetchAll(PDO::FETCH_ASSOC);

		$resultado = array(
			'categorias' => $categorias
		);

		echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
	}
	function excluir($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);
			
		$atividade = $this->conexao->prepare('DELETE FROM atividade WHERE idatividade = :idatividade');
		$atividade->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_INT);

		if(!$atividade->execute()){
			throw new Exception("Invalid Request;Atenção, nenhum dado foi deletado!", 400);
		}
	}

	function editar($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);

		try{
			$editar = $this->conexao->prepare('SELECT COUNT(*) as qtd FROM atividade as a WHERE a.idatividade = :idatividade');

			$editar->bindParam(':idatividade', param_filter($KEYS['idatividade'],'int'), PDO::PARAM_INT);
			$editar->execute();

			if($editar->fetch(PDO::FETCH_ASSOC)['qtd'] > 0){
				$editar = $this->conexao->prepare('UPDATE atividade
					SET
						idcategoria = :idcategoria,
						descricao = :descricao,
						hora_inicio = :hora_inicio,
						hora_fim = :hora_fim,
						titulo = :titulo
					WHERE idatividade = :idatividade');

				$editar->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_INT);
				$editar->bindParam(':idcategoria', $KEYS['idcategoria'], PDO::PARAM_INT);
				$editar->bindParam(':descricao', $KEYS['descricao'], PDO::PARAM_STR);
				$editar->bindParam(':hora_inicio', $KEYS['hora_inicio'], PDO::PARAM_STR);
				$editar->bindParam(':hora_fim', $KEYS['hora_fim'], PDO::PARAM_STR);
				$editar->bindParam(':titulo', $KEYS['titulo'], PDO::PARAM_STR);
				$editar->execute();
			}
			else{
				throw new Exception("Invalid Request;Atenção, nenhum dado foi editado!", 400);
			}
		}catch(Exception $e){
			throw $e;
		}
	}
}

