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
                swal({
                    title: "Sucesso",
                    text: "Login autorizado, redirecionando",
                    type: "success"
                })
                setTimeout(function(){    window.location.href = "./home.html"; },1000);
                  
            },
            function(e){
                if(e == 401){
                    swal({
                        title: "Login não autorizado",
                        text: "E-mail ou senha inválidos",
                        type: "error"
                    })
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
            swal({
                title: "Sucesso",
                text: "Usuário criado",
                type: "success"
            })
            setTimeout(function(){ window.location.href = "./login.html"; }, 1000);
            
        }, function(){
            swal({
                title: "Usuário já cadastrado",
                text: "Seus dados de e-mail e/ou CPF já se encontram no nosso banco de dados",
                type: "error"
            })
            // setTimeout(function(){ window.location.href = "./cadastro.html"; }, 1000);           
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
        this.requisicao('recuperarSenha', keys, function(){
            alert("Mensagem enviada, verifique seu e-mail");
            return true;
        }, function(e){
            if(e == 404)alert("Email inválido");
        });
    }

    alterarSenha(){
        this.requisicao('alterarSenha', function(){
            alert("Senha Alterada com sucesso, cheque seu e-mail!");
            return true;
        }, function(e){
            if(e == 500)alert("Não foi possível alterar a senha!");
        });
    }

    nivelPermissao(){
        return JSON.parse(this.requisicao('nivelPermissao'));
    }

    inscricao(idatividade, inscreveu, erro){
        var keys = {
            idatividade: idatividade,
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
       
        this.xmlhttp.addEventListener("readystatechange", (ev)=>{
            if(ev.target.readyState === 4) {
                if(ev.target.status === 200) { //Se der 200 siginifica que está logado     
                    if(callback(ev.target.responseText)) resposta = ev.target.responseText;
                }else{
                    if(ev.target.status === 409 || ev.target.status === 401){
                        swal({
                            title : "Erro",
                            text: "Acesso negado",
                            type: "error"
                        })
                        setTimeout(()=>{
                            window.location.href = "./login.html";
                        }, 2000)
                    }    
                    else if(ev.target.status === 500){
                        swal({
                            title : "Erro",
                            text: "Erro interno no servidor, entre em contato conosco"
                        })  
                        setTimeout(()=>{
                            window.location.href = "./login.html";
                        }, 2000)
                    }
                }                       
            }
        })   
        this.xmlhttp.send(post ? JSON.stringify(post) : null);
        return resposta;
    }
}