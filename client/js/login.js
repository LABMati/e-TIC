
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

esqueciSenha.addEventListener("click", async ()=>{
	let email = await swal({
		title: "Digite seu e-mail cadastrado",
		input: 'email',
		inputPlaceholder: "E-mail",
		showCancelButton: true,
		cancelButtonColor: '#d33'
	})

	usuario.recuperarSenha(await email.value)
})

// esqueciSenha.addEventListener('click', async function(){
// 	let email = await swal({
// 		title: 'Digite seu email cadastrado',
// 		input: 'email',
// 		inputPlaceholder: 'E-mail'
// 	})
// 	console.log(email)
// 	if(email)
// 		usuario.recuperarSenha(email.value)
// 	else{
// 		swal(
// 			{
// 				title: "Erro",
// 				type: "error",
// 				text: "Não foi possível enviar seu email, tente novamente ou entre em contato conosco"
// 			}
// 		)
// 	}

// });

window.addEventListener('load', () => {
	let ratio = window.innerWidth / window.innerHeight
	if (ratio < 9 / 7) {
		let link = document.createElement('link')
		link.rel = "stylesheet"
		link.href = "css/mobile-login.css"
		document.head.appendChild(link)
	}
		
})