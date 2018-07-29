
var btnEntrar = document.querySelector(".btnEntrar");
var formulario = document.querySelector(".form");
var esqueciSenha = document.querySelector(".a_esqueciSenha");

var usuario = new Usuario();

btnEntrar.addEventListener('click', function(){
    var login = document.querySelector("#login").value;
    var senha = document.querySelector("#senha").value;
    usuario.login(login,senha);
});

formulario.addEventListener('keypress', function(k){
	if(k.key == "Enter"){
		k.preventDefault();	
		var login = document.querySelector("#login").value;
    	var senha = document.querySelector("#senha").value;
		usuario.login(login,senha)	
	}
});
esqueciSenha.addEventListener('click', function(){
	usuario.recuperarSenha(prompt('Digite seu endereço de email:'));
});
