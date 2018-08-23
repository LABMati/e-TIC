<?php

class Atividade extends Conexao{

	protected $idusuario;

	// Retorna todas as atividades cadastradas no banco, juntamente com as atividades em que o usuário requisitor está cadastro bem como suas filas

	function carregar($token){
		parent::auth($token, Conexao::NORMAL);
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
		$inscricoes2 = [];
		$iterator = 0;
		$filas = array(
			'idatividade' => [],
			'posicao' => []
		);

		while($row = $inscricoes->fetch(PDO::FETCH_ASSOC)){
			array_push($inscricoes2, $row['idatividade']);

			
			$vagasDisponiveis = $this->conexao->prepare('SELECT (atividade.capacidade - count(usuario_atividade.idusuario)) as vagas from atividade INNER JOIN usuario_atividade ON atividade.idatividade = usuario_atividade.idatividade WHERE atividade.idatividade = :idatividade'); 
			$vagasDisponiveis->bindParam(':idatividade', $row['idatividade'], PDO::PARAM_STR);
			$vagasDisponiveis->execute();
			$vagasDisponiveis = $vagasDisponiveis->fetch(PDO::FETCH_ASSOC);
			$tamFila = $vagasDisponiveis['vagas']*-1;

			if($vagasDisponiveis>0){
				$fila = $this->conexao->prepare("SELECT u.idusuario FROM atividade AS a INNER JOIN usuario_atividade AS ua ON ua.idatividade=a.idatividade INNER JOIN usuario u ON u.idusuario=ua.idusuario WHERE ua.idatividade=:idAtividade ORDER BY(ua.hora_inscricao) DESC LIMIT :tamFila");
				$fila->bindParam(':idAtividade', $row['idatividade'], PDO::PARAM_INT);
				$fila->bindParam(':tamFila', $tamFila, PDO::PARAM_INT);
				$fila->execute();
				$fila = $fila->fetchAll(PDO::FETCH_ASSOC);
				$fila = array_reverse($fila);
				$posFila = 0;
				for($i=0;$i<sizeof($fila);$i++){
					if($idusuario['idusuario']==$fila[$i]['idusuario'])
						$posFila=$i+1;
				}
				if($posFila != 0){
					array_push($filas['idatividade'], $row['idatividade']);
					array_push($filas['posicao'], $posFila);
				}
			}
		}
			
		//PEGA ATIVIDIDADES CADASTRADAS
		$atividades = $this->conexao->prepare('SELECT atividade.idatividade, categoria.nome as categoria, titulo, descricao,hora_inicio, hora_fim,
				(SELECT atividade.capacidade - count(idusuario)
				FROM usuario_atividade
				WHERE usuario_atividade.idatividade = atividade.idatividade ) as vagasDisponiveis, capacidade
			FROM atividade, categoria
			WHERE categoria.idcategoria = atividade.idcategoria ORDER BY hora_inicio ASC');
		$atividades->execute();
		$atividades = $atividades->fetchAll(PDO::FETCH_ASSOC);

		$resultado = array(
			'atividades' => $atividades,
			'inscricoes' => $inscricoes2,
			'filas' => $filas
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
	}

	// Cria uma atividade no banco de dados com as informações passadas no JSON do payload

	function cadastro($KEYS, $token){
		parent::auth($token, Conexao::ADMIN);

		try{
			$cadastro = $this->conexao->prepare('INSERT INTO atividade(idcategoria, descricao, capacidade, hora_inicio, hora_fim, titulo) Values(:idcategoria, :descricao, :capacidade, :hora_inicio, :hora_fim, :titulo/*, :idusuario*/)');
			$cadastro->bindParam(':idcategoria', param_filter($KEYS['idcategoria'],'int'), PDO::PARAM_STR);
			$cadastro->bindParam(':descricao', param_filter($KEYS['descricao'],'str'), PDO::PARAM_STR);
			$cadastro->bindParam(':capacidade', param_filter($KEYS['capacidade'],'int'), PDO::PARAM_INT);
			$cadastro->bindParam(':hora_inicio', param_filter($KEYS['hora_inicio'],'str'), PDO::PARAM_STR);
			$cadastro->bindParam(':hora_fim', param_filter($KEYS['hora_fim'],'str'), PDO::PARAM_STR);
			$cadastro->bindParam(':titulo', param_filter($KEYS['titulo'],'str'), PDO::PARAM_STR);
			$cadastro->execute();
		}catch(Exception $e){
			throw $e;
		}
	}


	// Devolve todos os inscritos de uma atividade X recebendo o ID dessa atividade

	function carregarInscritos($id){
		try{
			 
			$query = $this->conexao->prepare("SELECT u.idusuario as id, u.nome, ua.presenca FROM usuario AS u INNER JOIN usuario_atividade as ua ON u.idusuario = ua.idusuario WHERE ua.idatividade = ? ORDER BY ua.hora_inscricao");

			$query->bindParam(1, $id, PDO::PARAM_STR);
			$query->execute();
			$response = $query->fetchAll(PDO::FETCH_ASSOC)

			if($query->rowCount() > 0){
				echo json_encode($response);
			}else{
				die("O ID enviado não está cadastrado ou não possui usuários inscritos");
			}
				
		}catch(PDOException $e){
			die("Erro" . $e->getMessage());
		}
	}

	function carregarInscritosGeral(){
		try{
			 
			$query = $this->conexao->prepare("SELECT u.idusuario as id, u.nome, ua.presenca, a.titulo as nome_atividade FROM usuario AS u INNER JOIN usuario_atividade as ua ON u.idusuario = ua.idusuario INNER JOIN atividade as a ON a.idatividade = ua.idatividade ORDER BY a.titulo, ua.hora_inscricao");

			$query->bindParam(1, $id, PDO::PARAM_STR);
			$query->execute();
			$response = $query->fetchAll(PDO::FETCH_ASSOC)

			if($query->rowCount() > 0){
				echo json_encode($response);
			}else{
				die("O ID enviado não está cadastrado ou não possui usuários inscritos");
			}
				
		}catch(PDOException $e){
			die("Erro" . $e->getMessage());
		}
	}

	// Devolve uma atividade com seu id, titulo, e valor em eticoins a partir de um ID passado

	function carregarAtividadeUnica($id){
		try{
			$query = $this->conexao->prepare("SELECT a.idatividade, a.titulo as nome, c.valor as eticoin FROM atividade AS a INNER JOIN categoria as c ON a.idcategoria = c.idcategoria WHERE a.idatividade = ?");

			$query->bindParam(1, $id, PDO::PARAM_STR);
			$query->execute();
			$response = $query->fetchAll(PDO::FETCH_ASSOC)

			if($query->rowCount() > 0){
				echo json_encode($response);
			}else{
				die("O ID enviado não está cadastrado ou não possui usuários inscritos");
			}
				
		}catch(PDOException $e){
			die("Erro" . $e->getMessage());
		}
	}

	// Retorna todas as atividades com seu id, título e valor em eticoins

	function carregarTodasAtividades(){
		try{
			$query = $this->conexao->prepare("SELECT a.idatividade, a.titulo as nome, c.valor as eticoin FROM atividade AS a INNER JOIN categoria as c ON a.idcategoria = c.idcategoria");
			$query->execute();
			$response = $query->fetchAll(PDO::FETCH_ASSOC)

			if($query->rowCount() > 0){
				echo json_encode($response);
			}else{
				die("O ID enviado não está cadastrado ou não possui usuários inscritos");
			}
				
		}catch(PDOException $e){
			die("Erro" . $e->getMessage());
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
					SET usuario_atividade.presenca = CURRENT_TIMESTAMP()
					WHERE usuario_atividade.idatividade = :idatividade
					AND usuario_atividade.idusuario = :idusuario
				');
		}
		else{
			$chamada = $this->conexao->prepare('
					UPDATE atividade, seguranca, usuario, usuario_atividade
					SET usuario_atividade.presenca = CURRENT_TIMESTAMP()
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


	// Modifica a atividade que já está cadastrada no banco, caso ela exista, com os dados passados pelo usuário 

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
						capacidade = :capacidade,
						hora_inicio = :hora_inicio,
						hora_fim = :hora_fim,
						titulo = :titulo
					WHERE idatividade = :idatividade');

				$editar->bindParam(':idatividade', $KEYS['idatividade'], PDO::PARAM_INT);
				$editar->bindParam(':idcategoria', $KEYS['idcategoria'], PDO::PARAM_INT);
				$editar->bindParam(':descricao', $KEYS['descricao'], PDO::PARAM_STR);
				$editar->bindParam(':capacidade', $KEYS['capacidade'], PDO::PARAM_STR);
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