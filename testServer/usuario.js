class Usuario{

    constructor(){
        this.xmlhttp = new XMLHttpRequest();
    }

    login(login,senha){
        var keys = {
            login: login,
            senha: senha
        };

        this.requisicao('login', keys, 
            function(r){
                var resposta = JSON.parse(r);
                window.sessionStorage.setItem('token', resposta.token);
                swal(
                    {
                        title: "Sucesso",
                        type: "success",
                        body: "Logando!"
                    }
                )
                setTimeout(function(){    window.location.href = "./home.html"; },1000);
                  
            },
            function(e){
                if(e == 401){
                    swal(
                        {
                            title: "Erro",
                            type: "success",
                            body: "Usuário ou senha inválido"
                        }
                    )
                    setTimeout(function(){
                        window.location.href = "./login.html";
                    },1000);
                }
                return true;
            }
        );
    }

    carregar(){
        return JSON.parse(this.requisicao('carregarUsuarios'));
    }

    cadastro(nome,email,senha,cpf,nascimento,turma,escola,empresa,deficiencia,indicacao){
        var keys = {
            nome: nome,
            email: email,
            senha: senha,
            cpf: cpf,
            nascimento: nascimento,
            turma: turma,
            escola: escola,
            empresa: empresa,
            deficiencia: deficiencia,
            indicacao:indicacao,
        };


        this.requisicao('cadastrarUsuario', keys, function(){
            swal(
                {
                    title: "Sucesso",
                    type: "success",
                    body: "Cadastro concluído"
                }
            )
            setTimeout(function(){ window.location.href = "./login.html"; }, 1000);
            
        }, function(){
            swal(
                {
                    title: "Erro",
                    type: "error",
                    body: "Usuário já cadastrado"
                }
            )
            return true;
        });
    }

    cadastrarApresentador(id, curriculo){
        var keys = {
            id: id,
            curriculo: curriculo
        };
        this.requisicao('cadastrarApresentador', keys)
    }

    carregarApresentadores(){
        return JSON.parse(this.requisicao('carregarApresentadores'));
    }

    alterarAutorizacao(idusuario, idtipo, alterou, erro){
        var keys = {
            idusuario: idusuario,
            idtipo: idtipo
        };
        JSON.parse(this.requisicao('alterarAutorizacao',keys, alterou, erro));

    }

    recuperarSenha(email){
        var keys = {
            email: email
        };
        this.requisicao('recuperarSenha', keys, ()=>{
            swal(
                {
                    title: "Sucesso",
                    type: "info",
                    body: "Mensagem enviada, verifique seu e-mail"
                }
            )
            return true;
        }, ()=>{
            swal(
                {
                    title: "Erro",
                    type: "error",
                    body: "E-mail inválido"
                }
            )
        });
    }

    alterarSenha(novaSenha){
        var keys = {
            senha: novaSenha
        };
        this.requisicao('alterarSenha', keys, function(){
            swal(
                {
                    title: "Sucesso",
                    type: "success",
                    body: "Senha alterada com sucesso"
                }
            )
            return true;
        }, function(e){
            if(e == 500){
                swal(
                    {
                        title: "Erro",
                        type: "error",
                        body: "Não foi possível alterar a senha"
                    }
                )
            }
        });
    }

    nivelPermissao(){
        return JSON.parse(this.requisicao('nivelPermissao'));
    }

    inscricao(idatividade, inscreveu, erro){
        var keys = {
            idatividade: idatividade
        };
         JSON.parse(this.requisicao('inscricao',keys, inscreveu, erro));
    }

    inscricaoForcada(idatividade,idusuario, inscreveu, erro){
        var keys = {
            idatividade: idatividade,
            idusuario: idusuario
        };
         JSON.parse(this.requisicao('inscricaoForcada',keys, inscreveu, erro));
    }
    
    desinscricao(idatividade){
        var keys = {
            idatividade: idatividade
        };
         return JSON.parse(this.requisicao('desinscricao',keys));
    }

    gerarCertificado(){
        window.location.href = '../server/router.php?option=gerarCertificado&token=' + window.sessionStorage.getItem('token');
    }


    requisicao(option, post, callback = function(){return true}, erro = function(){}){
        this.xmlhttp = new XMLHttpRequest();
        var resposta = false;
        var token = "&token=" + window.sessionStorage.getItem('token');
        this.xmlhttp.open("POST","../server/router.php?option=" + option + token, false);
       
        this.xmlhttp.onreadystatechange = function(e){
            if(e.target.readyState === 4) {
                if(e.target.status === 200) { //Se der 200 siginifica que está logado     
                    if(callback(e.target.responseText)) resposta = e.target.responseText;
                }else{
                    if(!erro(e.target.status)) window.location.href = "./login.html";
                }                       
            }
        }   
        this.xmlhttp.send(post ? JSON.stringify(post) : null);
        return resposta;
    }
}

