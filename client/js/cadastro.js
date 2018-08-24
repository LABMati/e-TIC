
var btn_cadastrar = document.querySelector("#btn_cadastrar");
var usuario = new Usuario();

VMasker(document.querySelector("#cpf")).maskPattern("999.999.999-99")
VMasker(document.querySelector("#nascimento")).maskPattern("99/99/9999")

btn_cadastrar.addEventListener("click", 
    ()=> {
        let cpf   = document.querySelector("#cpf").value;
        let nome  = document.querySelector("#nome").value;
        let email = document.querySelector("#email").value;
        let senha = document.querySelector("#senha").value;
        let r_senha = document.querySelector("#r-senha").value;
        let nascimento = document.querySelector("#nascimento").value;
        let escola = document.querySelector("#escola").value;
        let empresa = document.querySelector("#empresa").value;
        let deficiencia = document.querySelector("#deficiencia").value;
        let turma = document.querySelector("#turma").value;
        let indicacao = document.querySelector("#indicacao").value;

        if(cpf.trim().length < 14){
            return false;
        }
        if(nome.trim() == ""){
            return false;
        }

        if(senha.trim() != r_senha.trim() || senha.trim() == ""){
            swal("ERRO","Senhas são diferentes","error");
            return false;
        }
        if(checkMail(email)) 
            usuario.cadastro(nome, email, senha, cpf, nascimento, turma, escola, empresa, deficiencia, indicacao)
        else 
            console.log(false)
    }
)


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

window.addEventListener('load', () => {
    if (window.innerWidth / window.innerHeight < 9 / 7) {
        let link = document.createElement('link')
        link.rel = "stylesheet"
        link.href = "css/mobile-cadastro.css"
        document.head.appendChild(link)
    }
})