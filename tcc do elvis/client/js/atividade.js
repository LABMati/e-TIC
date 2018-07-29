
class Atividade{

    constructor(){
        this.xmlhttp = new XMLHttpRequest();
    }

    cadastrar(idcategoria, titulo, descricao, capacidade, h_inicio, h_fim /*,idusuario*/,certo,errado){
        var keys = {
            idcategoria: idcategoria,
            titulo: titulo,
            descricao: descricao,
            capacidade: capacidade,
            hora_inicio: h_inicio,
            hora_fim: h_fim,
            /*idusuario : idusuario*/
        }
        this.requisicao('cadastrarAtividade', keys, certo, errado);
    }

    editar(idatividade, idcategoria, titulo, descricao, capacidade, h_inicio, h_fim){
        var keys = {
            idcategoria: idcategoria,
            titulo: titulo,
            descricao: descricao,
            capacidade: capacidade,
            hora_inicio: h_inicio,
            hora_fim: h_fim,
            idatividade: idatividade
            /*idusuario : idusuario*/
        }
        this.requisicao('editarAtividade', keys, function(){alert('Atividade apagada com sucesso!')}, function(code){
            if(code == 400){
                alert("Nenhuma atividade atualizada!");
                return true;
            }
            else return false;
        });
    }

    listar(dados){
        alert(dados);
        return;
    }

    excluir(id){
        var keys = {
            idatividade: id
        }
        this.requisicao('excluirAtividade', keys, function(){alert('Atividade apagada com sucesso!')}, function(code){
            if(code == 400){
                alert("Nenhuma atividade deletada!");
                return true;
            }
            else return false;
        });
    }

    carregar() {

        return JSON.parse(this.requisicao('carregarAtividades'));
    }
    carregarCategorias() {

        return JSON.parse(this.requisicao('carregarCategorias'));
    }

    carregarForcado(id,certo,errado) {

        var keys = {
            idusuario: id
        }
        return JSON.parse(this.requisicao('carregarForcado',keys, certo, errado));
    }
    carregarAtividadesApresentador(){
        return JSON.parse(this.requisicao('carregarAtividadesApresentador'));
    }

    carregarChamada(idatividade){
        var keys = {
            idatividade: idatividade
        }
        return JSON.parse(this.requisicao('carregarChamada', keys));
    }

    fazerChamada(idatividade, idusuario, certo, errado){
        var keys = {
            idatividade: idatividade,
            idusuario: idusuario
        }
        this.requisicao('fazerChamada', keys, certo, errado);
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