<?php

header("Access-Control-Allow-Origin: *");

// Devolve todos os inscritos de uma atividade X recebendo o ID dessa atividade

$conexao = new PDO('mysql:host=localhost;dbname=etic2018;charset=utf8','etic','ifc#tic@753');

switch($_GET['option']){
    case "carregarInscritos":
        try{
            $capacidade = $conexao->prepare("SELECT capacidade from atividade where idatividade = ?");
            $capacidade->bindParam(1, $_GET['id'], PDO::PARAM_STR);
            $capacidade->execute();
            $capacidade = $capacidade->fetch(PDO::FETCH_ASSOC);


            $conexao->exec("SET @row_num = 0");
            $query = $conexao->prepare("SELECT * FROM (SELECT @row_num := @row_num+1 AS num, @row_num>{$capacidade['capacidade']} AS espera, u.idusuario as id, u.nome FROM usuario AS u INNER JOIN usuario_atividade as ua ON u.idusuario = ua.idusuario WHERE ua.idatividade = ?) AS x ORDER BY nome");
            
            $query->bindParam(1, $_GET['id'], PDO::PARAM_STR);
            $query->execute();
            
            $response = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $presenca = $conexao->prepare("SELECT UNIX_TIMESTAMP (ua.presenca) as presenca FROM usuario_atividade as ua where ua.idusuario =". $row['id'] . " AND ua.idatividade =" . $_GET['id']);
                $presenca->execute();
                $presenca = $presenca->fetch(PDO::FETCH_ASSOC);
                $row['presenca'] = $presenca['presenca'];
                array_push($response, $row);
            }

            if($query->rowCount() > 0){
                die( json_encode($response) );
            }else{
                die("O ID enviado não está cadastrado ou não possui usuários inscritos"); 
                // caiu aqui
            }
                
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;

    case "carregarInscritosGeral":
        try{
            
            $query = $conexao->prepare("SELECT u.idusuario as id, u.nome, ua.presenca, a.titulo as nome_atividade FROM usuario AS u INNER JOIN usuario_atividade as ua ON u.idusuario = ua.idusuario INNER JOIN atividade as a ON a.idatividade = ua.idatividade ORDER BY a.titulo, ua.hora_inscricao");
            $query->execute();
            $response = $query->fetchAll(PDO::FETCH_ASSOC);

            if($query->rowCount() > 0){
                echo json_encode($response);
                die;
            }else{
                die("O ID enviado não está cadastrado ou não possui usuários inscritos");
            }
                
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;

    case "carregarAtividadeUnica":
        try{
            $query = $conexao->prepare("SELECT a.idatividade, a.titulo as nome, c.valor as eticoin FROM atividade AS a INNER JOIN categoria as c ON a.idcategoria = c.idcategoria WHERE a.idatividade = ?");

            $query->bindParam(1, $_GET['id'], PDO::PARAM_STR);
            $query->execute();
            $response = $query->fetch(PDO::FETCH_ASSOC);

            if($query->rowCount() > 0){
                echo json_encode($response);
                die;
            }else{
                die("O ID enviado não está cadastrado ou não possui usuários inscritos");
            }
                
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;

    case "carregarTodasAtividades":
        try{
            $query = $conexao->prepare("SELECT a.idatividade, a.titulo as nome, c.valor as eticoin FROM atividade AS a INNER JOIN categoria as c ON a.idcategoria = c.idcategoria");
            $query->execute();
            $response = $query->fetchAll(PDO::FETCH_ASSOC);

            if($query->rowCount() > 0){
                echo json_encode($response);
                die;
            }else{
                die("O ID enviado não está cadastrado ou não possui usuários inscritos");
            }
                
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;
    
    case "carregarUsuarios":
        try{
            $query = $conexao->prepare("SELECT idusuario, nome, email FROM usuario");
            $query->execute();
            $response = $query->fetchAll(PDO::FETCH_ASSOC);

            if($query->rowCount() > 0){
                echo json_encode($response);
                die;
            }else{
                die("O ID enviado não está cadastrado ou não possui usuários inscritos");
            }
                
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;
    case "carregarEticoins":
        try{
            $query = $conexao->prepare("SELECT u.idusuario, u.nome,sum(c.valor) as eticoins FROM usuario u
                INNER JOIN usuario_atividade ua
                ON ua.idusuario = u.idusuario
                INNER JOIN atividade a
                ON a.idatividade=ua.idatividade
                INNER JOIN categoria c
                ON c.idcategoria=a.idcategoria
                WHERE ua.presenca > 0 AND u.idusuario=?
                GROUP BY u.idusuario;");
            $query->bindParam(1,$_GET['id'],PDO::PARAM_INT);
            $query->execute();
            $response = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() > 0){
                die(json_encode($response));
            }else{
                $query = $conexao->prepare("SELECT idusuario, nome FROM usuario WHERE id_usuario=?");
                $query->bindParam(1,$_GET['id'],PDO::PARAM_INT);
                $response = $query->fetch(PDO::FETCH_ASSOC);
                die(json_encode([
                    'idusuario'=>$response['idusuario'],
                    'nome'=>$response['nome'],
                    'eticoins'=>0                                        
                ]));
            }
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;

    case "carregarEticoinsGeral":
        try{
            $query = $conexao->prepare("SELECT u.idusuario, u.nome,sum(c.valor) as eticoins FROM usuario u
                INNER JOIN usuario_atividade ua
                ON ua.idusuario = u.idusuario
                INNER JOIN atividade a
                ON a.idatividade=ua.idatividade
                INNER JOIN categoria c
                ON c.idcategoria=a.idcategoria
                GROUP BY u.idusuario
                ORDER BY eticoins;");
            $query->execute();
            $response = $query->fetchAll(PDO::FETCH_ASSOC);
            if($query->rowCount() > 0){
                die(json_encode($response));
            }else{
                die(json_encode(['eticoins'=>0]));
            }
        }catch(PDOException $e){
            die("Erro" . $e->getMessage());
        }
    break;
}