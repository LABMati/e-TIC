<?php

class Reports extends Conexao{

    protected $payload;
    protected $response;

    function authenticate($keys, $token){
        if(parent::auth($token, Conexao::ADMIN)){
            $this->payload = json_decode($keys, true);
            $this->setup();
        }
        else{
            http_response_code(403);
            die("Não autorizado");
        }
    }

    function setup(){
        switch($this->payload['option']){
            case "listarUsuarios":
                $this->run('SELECT idusuario, nome, email, cpf FROM usuario');
                break;
            case "numeroDeUsuarios":
                $this->run('SELECT count(idusuario) qtd,indicacao FROM usuario WHERE indicacao != 0 GROUP BY(indicacao) ORDER BY(qtd) DESC LIMIT 15');
                break;
            default:
                break;
        }
    }

    function run($query){
        try{        
            $report = $this->conexao->prepare($query);

            // for ($i = 0; $i < count($this->payload->param); $i++) { 
            //     $report->bindParam($i, $this->payload->param[$i], PDO::PARAM_STR);
            // }
            $report->execute();
            $this->response = $report->fetchAll(PDO::FETCH_NUM);
            echo(json_encode($this->response));
        }catch(Exception $e){
            die($e);
        }
    }
}