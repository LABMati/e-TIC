
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
            hora_fim: h_fim
        }
        this.requisicao('cadastrarAtividade', keys, certo, errado);
    }

    async editar(idatividade){
        let jsao = JSON.parse(this.xmlhttp.response)
        let target

        for (let i = 0; i < jsao.atividades.length; i++) {
            if(idatividade == jsao.atividades[i].idatividade){
                target = jsao.atividades[i]
                break;
            }
        }
        target.editValues = await swal({
            title: "Editar atividade",
            html:
            "<div class='input-group-sw'>"+ 
            
                "<label for='title'> Título </label>"+
                "<input id='title' type='text' class='sw-input' value='"+ target.titulo +"'>" +
            
                "<label for='category'>Categoria</label>"+
                "<select id='category'>"+
                    "<option>Escolha uma categoria</option>"+
                    "<option>Palestra</option>"+
                    "<option>Minicurso</option>"+
                    "<option>Hackathon</option>"+
                    "<option>Jogos</option>"+
                    "<option>Cerimônia</option>"+
                "</select>"+

                "<label for='description'>Descrição (Max. 255)</label>"+
                "<textarea id='description' class='sw-input' maxlenght='255'>"+ target.descricao +"</textarea>" +
            
                "<label for='h-ini'>Inicio</label>"+
                "<input id='h-ini' type='datetime-local' class='sw-input' value='"+ target.hora_inicio +"'>"+
            
                "<label for='h-fim'>Fim</label>"+
                "<input id='h-fim' type='datetime-local' class='sw-input' value='"+ target.hora_fim +"'>"+
            
            "</div>",
            preConfirm: ()=>{
                return [
                    document.querySelector("input#title").value,
                    document.querySelector("select#category").selectedIndex,
                    document.querySelector("textarea#description").value,
                    document.querySelector("input#h-ini").value,
                    document.querySelector("input#h-fim").value,
                ]
            }
        })

        var keys = {
            titulo: target.editValues.value[0],
            idcategoria: target.editValues.value[1],
            descricao: target.editValues.value[2],
            hora_inicio: target.editValues.value[3],
            hora_fim: target.editValues.value[4],
            idatividade: idatividade
        }

        this.requisicao('editarAtividade', keys, 
        ()=>{
            swal({
                title: "Sucesso",
                type: "success",
                text: "Atividade editada com sucesso"
            })
        }, 
        (code)=>{
            if(code == 400){
                swal({
                    title: "Erro",
                    type: "error",
                    text: "Ocorreu um erro ao atualizar a atividade" 
                })
                return true;
            }
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
        this.xmlhttp.open("POST","../testServer/router.php?option=" + option + token, false);
       
        this.xmlhttp.onreadystatechange = function(e){
            if(e.target.readyState === 4) {
                if(e.target.status === 200) { //Se der 200 siginifica que está logado     
                    if(callback(e.target.responseText)) resposta = e.target.responseText;
                }else{
                    // if(!erro(e.target.status)) window.location.href = "./login.html";
                }                      
            }
        }

        this.xmlhttp.send(post ? JSON.stringify(post) : null);
        return resposta;
    }    
}