
 var btn_cadastrar = document.querySelector("#btn_cadastrar");
 var usuario = new Usuario();

 // email.addEventListener("change", function(){
 // 	if(checkMail(this)){
 // 		// alert("FUnfou")
 // 	}else{
 // 		alert("ERRO","Email inválido", "pastel-danger");
 // 	}
 // });


 btn_cadastrar.addEventListener("click",function(){
 	var cpf   = document.querySelector("#cpf").value;

 	var nome  = document.querySelector("#nome").value;

 	var email = document.querySelector("#email").value;

 	var senha = document.querySelector("#senha").value;
    var r_senha = document.querySelector("#r-senha").value;
    var nascimento = document.querySelector("#nascimento").value;
    var escola = document.querySelector("#escola").value;
    var empresa = document.querySelector("#empresa").value;
    var deficiencia = document.querySelector("#deficiencia").value;
    var turma = document.querySelector("#turma").value;
    var indicacao = document.querySelector("#indicacao").value;

    if(cpf.length < 14){
        alert("ERRO","CPF inválido", "pastel-danger");
        return false;
    }
    if(nome == ""){
        alert("ERRO","Preecha o nome", "pastel-danger");
        return false;
    }
    //  if(nascimento == ""){
    //     alert("ERRO","Preecha o nome", "pastel-danger");
    //     return false;
    // }
    if(!verificaSenha(senha,r_senha) || senha == ""){
        alert("ERRO","Senhas são diferentes", "pastel-danger");
        return false;
    }
 	if(checkMail(email)){
        usuario.cadastro(nome, email, senha, cpf, nascimento, turma, escola, empresa, deficiencia, indicacao);
    }else{
        alert("ERRO","Email inválido", "pastel-danger");
    }

 });

//Aqui vai aquele JQuery básico...
function verificaSenha(a,b){
    if(a == b){
        return true;
    }
    return false;
}

function checkMail(mail){
	var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);

	if(typeof(mail) == "string"){
		if(er.test(mail)){
			return true; 
		}	
	}else if(typeof(mail) == "object"){
		if(er.test(mail.value)){
			return true;
		}	
	}else{
		return false;
	}
}

$(function(){
        function validCPF (cpf) {
          return cpf.match(/^\d{3}\.?\d{3}\.?\d{3}\-?\d{2}$/);
        }
        
        $("#nascimento").mask("00/00/0000");
        $("#cpf").mask("000.000.000-00");

      });

