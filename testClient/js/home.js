let c_panel = {
	listarAtividades : document.querySelector("#btnListarAtividades"),
	btnInscreverAtividade : document.querySelector("#btnInscreverAtividade")
}

let c_atividade = new Atividade();
let c_usuario = new Usuario();

var modal = document.getElementById('myModal');
var span = document.getElementsByClassName("close")[0];
var main = document.querySelector("div.main");
let menu = document.querySelector("div.menu")
let menuTrigger = document.querySelector("div.more-menu i")
let menuOpen = false

menuTrigger.addEventListener("click", (ev)=>{
	if(!menuOpen){
		ev.stopImmediatePropagation()
		menu.style.display = 'block'
		menuOpen = !menuOpen
	}
})

window.addEventListener("load", ()=>{
	if(getAllUrlParams().token !== undefined){
		window.sessionStorage.setItem('token', getAllUrlParams().token);
		c_usuario.alterarSenha()
	}
	listarAtividades()
})

// Password alteration



span.onclick = ()=> {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
	}
	if (menuOpen && !event.path.includes(menu)){
		menuOpen = !menuOpen
		menu.style.display = 'none'
	}
}


//GAMBIARRA PRECISA SER PARADAAAA
function jaTaInscrito(val,btn = null){
	if(val.innerText == "Inscrito"){
		val.innerText = "Inscrever-se";
		val.style.background = "#631725";
		if(btn){
			alert("Sucesso","Você foi desinscrito","pastel-info");
		}		
	}
	else{
		val.innerText = "Inscrito";
		val.style.background = "rgb(22, 64, 27)";
		val.disabled = false;
		if(btn){
			alert("Sucesso","Inscricão realizada","pastel-success");
		}
	}
}

function jaTaPresente(val){
	if((val.value == "faltou")||(val.value == 0)) {
		val.value = "faltou";
		val.style.background = "#631725";
	}
	else{
		val.value = "presente";
		val.style.background = "rgb(22, 64, 27)";
	}	
}

function mudarPresenca(val){
	if((val.value == "faltou")||(val.value == 0) || (val.innerText == "faltou")||(val.innerText == 0)) {	
		val.innerText = "presente";
		val.value = "presente";
		val.style.background = "#317b3a";
	}
	else{
		val.innerText = "faltou";
		val.value = "faltou";
		val.style.background = "black";
	}
}
function listarAtividades(){

	let nivel = c_usuario.nivelPermissao().nivel;

	let lista = c_atividade.carregar();	
	var containerAtividade = document.createElement('div');
	main.appendChild(containerAtividade).classList.add('containerMain');

	let headerListaAtividade = document.createElement('h1');
	headerListaAtividade.innerText = "Escolha suas atividades";
	containerAtividade.appendChild(headerListaAtividade).classList.add('headerMain');

	containerAtividade.classList.add('transicaoEntrada');
	let L_atividades = lista['atividades'];
	// let L_categorias = lista['categorias'];
	let L_inscricoes = lista['inscricoes'];
	
	if(L_atividades.length > 0){
		L_atividades.forEach(function (linha) {

			let atividade = document.createElement('div');
			containerAtividade.appendChild(atividade).classList.add('atividade');

			let divTitulo = document.createElement('div');
			atividade.appendChild(divTitulo).classList.add('divTituloListarAtividades');

			let titulo = document.createElement('span');
			titulo.classList.add('tituloListarAtividades');
			divTitulo.appendChild(titulo).innerText = linha['titulo'];

			if (nivel == 1) {
				// titulo.width = '80%';
				let editar = document.createElement('i');
				editar.classList.add('editarListarAtividades', 'fas', 'fa-pencil-alt');
				divTitulo.appendChild(editar);
				editar.addEventListener("click", ()=>{
					c_atividade.editar(btnInscrever.id)
				})

				let excluir = document.createElement('i');
				excluir.classList.add('excluirListarAtividades', 'fas', 'fa-times');
				divTitulo.appendChild(excluir);
				excluir.onclick = function () {
					c_atividade.excluir(btnInscrever.id);
					mataOsFilhos(main);
					listarAtividades();
				}
			}
		
			let categoria = document.createElement('h3');
			atividade.appendChild(categoria).innerText = linha['categoria'].charAt(0).toUpperCase() + linha['categoria'].slice(1);


			let horario = document.createElement('span');
			let h_inicio = linha['hora_inicio'].split(" ");

			let data_inicio = h_inicio[0].split("-");
			let h_fim = linha['hora_fim'].split(" ");
			h_inicio[1] = h_inicio[1].substring(0, h_inicio[1].length - 3)
			h_fim[1] = h_fim[1].substring(0, h_fim[1].length - 3)
		atividade.appendChild(horario).innerText = data_inicio[2]+ "/" + data_inicio[1]
		+ " | " + h_inicio[1] + "-" + h_fim[1];

		let btnAtividade = document.createElement('div');
		atividade.appendChild(btnAtividade).classList.add('btnAtividade');

		var btnInformacoes = document.createElement('button');
		btnAtividade.appendChild(btnInformacoes).innerText = "+Informações";

		btnInformacoes.onclick = ()=> {
			// document.querySelector("h3.modalTitulo").innerText = linha['titulo'];
			// document.querySelector("p.modalDescricao").innerText = linha['descricao'];
			// document.querySelector("span.modalData").innerHTML = "<strong>"+horario.innerText+"</strong>";
			// //document.querySelector("span.modalVagas").innerText = "Vagas: "+ linha['vagasDisponiveis'];
			// modal.style.display = "block";
			
			swal({
				title: linha['titulo'],
				text: linha['descricao'],
				footer: horario.innerText
			})
		}


		var btnInscrever = document.createElement('button');
		
		if(linha['vagasDisponiveis'] <= 0){
			btnAtividade.appendChild(btnInscrever).innerText = "Esgotado";
			btnInscrever.disabled = true;
			btnInscrever.style.backgroundColor  = "rgb(1, 41, 98)";
		}else{
			btnAtividade.appendChild(btnInscrever).innerText = "Inscrever-se";
		}
		btnInscrever.id = linha['idatividade'];

		
		L_inscricoes.forEach(function(linha){
			if(btnInscrever.id == linha['idatividade']){
				jaTaInscrito(btnInscrever);
			}
		});

		btnInscrever.onclick = function(){
			c_usuario.inscricao(this.id, function(){
				jaTaInscrito(btnInscrever,this);
			}, function(erro){
				if(erro == 409) alert("ERRO","Conflito de Horário ou Vagas esgotadas","pastel-danger");
				else window.location.href = "./login.html";
				return true;
			});
		}
	
	});
	}else{
		alert("Nenhuma atividade cadastrada");
	}
}	
	
	// if(nivel != 1){
	// 	let vetEditar = document.querySelectorAll('.editarListarAtividades');
	// 	let vetExcluir = document.querySelectorAll('.excluirListarAtividades');
	// 	vetEditar.forEach(function(edi){
	// 		edi.style.display = 'none';
	// 	});
	// 	vetExcluir.forEach(function(exc){
	// 		exc.style.display = 'none';
	// 	});
	// }

function mudarPermissao(){
	var u_usuarios = c_usuario.carregar()['usuarios'];
	var u_permissoes = c_usuario.carregar()['permissoes'];

	let containerMudarPermissao = document.createElement('div');
	main.appendChild(containerMudarPermissao).classList.add('containerMain','transicaoEntrada');

	let headerMudarPermissao = document.createElement('h1');
	headerMudarPermissao.innerText = "Alterar Permissões";
	containerMudarPermissao.appendChild(headerMudarPermissao).classList.add('headerMain');

	let table=document.createElement('table');
	let linha=document.createElement('tr');
	let nome=document.createElement('th');
	let email=document.createElement('th');
	let cpf=document.createElement('th');
	let permissao=document.createElement('th');
	containerMudarPermissao.appendChild(table).classList.add('tabelaPermissoes');
	table.appendChild(linha);
	linha.appendChild(nome).innerText = "nome";
	linha.appendChild(email).innerText = "email";
	linha.appendChild(cpf).innerText = "cpf";
	linha.appendChild(permissao).innerText = "permissão";

	u_usuarios.forEach(function(usuario){
		let linha=document.createElement('tr');
		table.appendChild(linha);
		let nome=document.createElement('th');
		let email=document.createElement('th');
		let cpf=document.createElement('th');
		let permissao=document.createElement('th');
		linha.appendChild(nome).innerText = usuario['nome'];
		linha.appendChild(email).innerText = usuario['email'];
		linha.appendChild(cpf).innerText = usuario['cpf'];
		linha.appendChild(permissao);
		let selectPermissao=document.createElement('select');
		selectPermissao.id = usuario['idusuario'];

		selectPermissao.addEventListener("change",function(){
			c_usuario.alterarAutorizacao(this.id, this.selectedOptions.item(0).id, function(){
				alert("Alterado com Sucesso!");
			}, function(erro){
				if(erro == 406) alert("Erro ao Mudar Permissao");
				else window.location.href = "./login.html";
				return true;			
			});
		
		});

		u_permissoes.forEach(function(t_permissao){
			let optionPermissao=document.createElement('option');
			optionPermissao.id = t_permissao['idtipo'];
			selectPermissao.appendChild(optionPermissao).innerText = t_permissao['nome'];
			if(t_permissao['idtipo'] == usuario['idtipo'] ){
				optionPermissao.selected = true;
			}
		});
		permissao.appendChild(selectPermissao);

	});
	// id nome cpf email admin senha certificado
	 
}

function chamada(){
	var atividadesApresentador = c_atividade.carregarAtividadesApresentador()['atividades'];

	let containerMinhasAtividades = document.createElement('div');
	main.appendChild(containerMinhasAtividades).classList.add('containerMain','transicaoEntrada');
	let titulo = document.createElement('h1');
	titulo.innerText = "Atividades Cadastradas";
	containerMinhasAtividades.appendChild(titulo).classList.add('headerMain');

	if(atividadesApresentador.length > 0){
		atividadesApresentador.forEach(function(apresentador){
			let btnAtividade = document.createElement('button');
			btnAtividade.id = apresentador['idatividade'];
			// btnAtividade.type = "button";
			containerMinhasAtividades.appendChild(btnAtividade).innerText = apresentador['titulo'];
			btnAtividade.classList.add('btnChamada');

			btnAtividade.addEventListener("click",function(){
				mataOsFilhos(containerMinhasAtividades);
				let btnImprimir = document.createElement('button');
				containerMinhasAtividades.appendChild(btnImprimir).classList.add('btnImprimir','noPrint');
				btnImprimir.innerText = "Imprimir";

				btnImprimir.onclick = function(){
					window.print();
					// window.location.href = "home.html";
				};

				let listaChamada = c_atividade.carregarChamada(this.id)['chamada'];

				let table=document.createElement('table');
				let linha=document.createElement('tr');
				let nome=document.createElement('th');
				let email=document.createElement('th');
				let presenca=document.createElement('th');

				containerMinhasAtividades.appendChild(table).classList.add('tabelaPermissoes');

				table.appendChild(linha);
				linha.appendChild(nome).innerText = "nome";
				linha.appendChild(email).innerText = "email";
				linha.appendChild(presenca).innerText = "presenca";

				listaChamada.forEach(function(t_chamada){
					let linha=document.createElement('tr');
					let nome=document.createElement('th');
					let email=document.createElement('th');
					let presenca=document.createElement('th');
					let btnPresenca=document.createElement('input');
					btnPresenca.id = t_chamada['idusuario'];
					btnPresenca.type = "button";
					btnPresenca.classList.add('noPrint');

					linha.appendChild(nome).innerText = t_chamada['nome'];
					linha.appendChild(email).innerText = t_chamada['email'];					
					linha.appendChild(presenca);
					
					presenca.appendChild(btnPresenca).value =t_chamada['presenca'];
					jaTaPresente(btnPresenca);
					table.appendChild(linha);
					btnPresenca.addEventListener("click",function(){
					
						c_atividade.fazerChamada(btnAtividade.id, btnPresenca.id, function(){
							mudarPresenca(btnPresenca);
						}, function(erro){
							alert("deu erro");
							if(erro == 406) alert("Erro ao fazer chamada");
							else window.location.href = "./login.html";
							return true;			
						});
					});
				});
				
			});
		});
	}else{
		alert("Você não tem atividades cadastradas :/")
	}
}


function criarAtividades(listaApresentadores){
	let criarAtividade = document.createElement('div');
	main.appendChild(criarAtividade).classList.add('criarAtividade','transicaoEntrada');

	let row = document.createElement('div');
	criarAtividade.appendChild(row).classList.add('row');
	let h1 = document.createElement('h1');
	h1.innerText="Adicionar Atividade";
	row.appendChild(h1);

	row = document.createElement('div');
	criarAtividade.appendChild(row).classList.add('row');
	let titulo = document.createElement('input');
	row.appendChild(titulo).placeholder = "Título";

	row = document.createElement('div');
	criarAtividade.appendChild(row).classList.add('row');
	let descricao = document.createElement('textarea');
	descricao.maxLength=255
	descricao.style.resize='vertical'
	row.appendChild(descricao).placeholder = "Descrição (Max. 255 caracteres)";

	divApresentador = document.createElement('div');
	criarAtividade.appendChild(divApresentador).classList.add('row');
	let inpApresentador = document.createElement('input');
	divApresentador.appendChild(inpApresentador).placeholder = "Apresentador";
	var divPaiApresentadores = document.createElement('div');
	divApresentador.appendChild(divPaiApresentadores).classList.add('divPaiApresentadores');


	inpApresentador.addEventListener("input", function(){
		mataOsFilhos(divPaiApresentadores);

		if(inpApresentador.value != "" ){
			
			listaApresentadores.forEach(function(apresentador){
				 if(JSON.stringify(apresentador['nome']).includes(inpApresentador.value)){
				 	let item = document.createElement('div');
				 	item.innerText = apresentador['nome'];
				 	item.id = apresentador['idusuario'];
				 	divPaiApresentadores.appendChild(item).onclick = function(){
				 		inpApresentador.value = item.innerText;
				 		inpApresentador.id = item.id;
						mataOsFilhos(divPaiApresentadores);
					};
				 }
			});
		}

	});

	row = document.createElement('div');
	criarAtividade.appendChild(row).classList.add('row');
	let categoria = document.createElement('select');
	row.appendChild(categoria);
	let optionDefault = document.createElement('option');
	optionDefault.disabled = true;
	optionDefault.selected = true;
	optionDefault.value = "";
	categoria.appendChild(optionDefault).innerText = "Categoria";

	let listaCategorias =   c_atividade.carregarCategorias()['categorias'];
	listaCategorias.forEach(function(cat){
		let optionPalestra = document.createElement('option');
		optionPalestra.innerText = cat.nome;
		categoria.appendChild(optionPalestra).value = cat.idcategoria;
	});

	let capacidade = document.createElement('input');
	capacidade.type = "number";
	row.appendChild(capacidade).placeholder = "Capacidade";

	row = document.createElement('div');
	criarAtividade.appendChild(row).classList.add('row');
	let data = document.createElement('input');
	data.type = "date";
	data.min = "1980-12-31";
	data.max = "2118-12-31";
	row.appendChild(data).placeholder = "data";
	let horaInicio = document.createElement('input');
	horaInicio.type ="time";
	horaInicio.value = "";
	row.appendChild(horaInicio).placeholder = "hora-inicio";
	let horaFim = document.createElement('input');
	horaFim.type ="time";
	horaFim.value = "";
	row.appendChild(horaFim).placeholder = "hora-fim";

	row = document.createElement('div');
	criarAtividade.appendChild(row).classList.add('row','btnCriarAtividade');
	let btnCancelar = document.createElement('button');
	row.appendChild(btnCancelar).innerText = "Cancelar";
	let btnSalvarAtividade = document.createElement('button');
	row.appendChild(btnSalvarAtividade).innerText = "Salvar Atividade";

	btnCancelar.onclick = function(){
		mataOsFilhos(main);
	}
	btnSalvarAtividade.onclick = function(){
		
		c_atividade.cadastrar(

		categoria.value,
		titulo.value,
		descricao.value,
		capacidade.value,
		horaInicio.value != "" && data.value != "" ? (data.value + " " + horaInicio.value) : "",
		horaFim.value != "" && data.value != "" ? (data.value + " " + horaFim.value) : "",

		/*inpApresentador.id,*/
		
		function(){
			alert("Sucesso", "Atividade criada", "pastel-success");
			mataOsFilhos(main);
			criarAtividades();
		},
		function(erro){
			
			if(erro == 404) alert("Erro","Dados inexistentos ou incorretos","pastel-danger");
			else window.location.href = "./login.html";
			return true;			
		});

	}
}


function mataOsFilhos(pai){
	while (pai.firstChild) {
   		pai.removeChild(pai.firstChild);
	}
}

function interfaceUsuario(){
	let nivel = c_usuario.nivelPermissao().nivel;
	if(nivel == 3){
		document.querySelector('#btnAutorizacoes').style.display = 'none';
		document.querySelector('#btnChamada').style.display = 'none';
		document.querySelector('#btnInscreverAtividade').style.display = 'none';
		document.querySelector('#btnGerarCertificado').style.display = 'none';
		document.querySelector('#btnEditarPerfil').style.display = 'none';
		document.querySelector('#btnChamadaForcada').style.display = 'none';
		document.querySelector('#btnRelatorios').style.display = 'none';
		// document.querySelector('#btnListarAtividades').style.display = 'none';
	}
	else if(nivel == 2){
		document.querySelector('#btnAutorizacoes').style.display = 'none';
		document.querySelector('#btnInscreverAtividade').style.display = 'none';
		document.querySelector('#gerarCertificado').style.display = 'none';
	}
}

function relatorios(){
	var containerRelatorios = document.createElement('div');
	main.appendChild(containerRelatorios).classList.add('containerMain');

	let headerRelatorios = document.createElement('h1');
	headerRelatorios.innerText = "Relatórios";
	containerRelatorios.appendChild(headerRelatorios).classList.add('headerMain');

	let listarUsuarios = document.createElement('button');
	listarUsuarios.innerText = "Listar Usuários";
	containerRelatorios.appendChild(listarUsuarios);

	listarUsuarios.onclick = function(){
		mataOsFilhos(containerRelatorios);

		let listaDeUsuarios = document.createElement('h1');
		listaDeUsuarios.innerText = "lista De Usuarios";
		containerRelatorios.appendChild(listaDeUsuarios).classList.add('headerMain');


		let btnImprimir = document.createElement('button');
		containerRelatorios.appendChild(btnImprimir).classList.add('btnImprimir','noPrint');
		btnImprimir.innerText = "Imprimir";	

		let u_usuarios = c_usuario.carregar()['usuarios'].sort(function(a, b){
			if(a.nome < b.nome) return -1;
			if(a.nome > b.nome) return 1;
			return 0;
		});

		let table=document.createElement('table');
		let linha=document.createElement('tr');
		let nome=document.createElement('th');
		let email=document.createElement('th');
		let cpf=document.createElement('th');
	
		containerRelatorios.appendChild(table).classList.add('tabelaPermissoes');
		table.appendChild(linha);
		linha.appendChild(nome).innerText = "nome";
		linha.appendChild(email).innerText = "email";
		linha.appendChild(cpf).innerText = "cpf";

		u_usuarios.forEach(function(usuario){
			let linha=document.createElement('tr');
			table.appendChild(linha);
			let nome=document.createElement('th');
			let email=document.createElement('th');
			let cpf=document.createElement('th');

			linha.appendChild(nome).innerText = usuario['nome'];
			linha.appendChild(email).innerText = usuario['email'];
			linha.appendChild(cpf).innerText = usuario['cpf'];							
		});

		btnImprimir.onclick = function(){
			window.print();

		}
		
		// window.location.href = "home.html";
	};

}

function chamadaForcada(){
	var containerChamadaForcada = document.createElement('div');
	main.appendChild(containerChamadaForcada).classList.add('containerMain');

	let headerChamadaForcada = document.createElement('h1');
	headerChamadaForcada.innerText = "Chamada Forçada";
	containerChamadaForcada.appendChild(headerChamadaForcada).classList.add('headerMain');

	let texto = document.createElement('span');
	texto.innerText = "Digite um nome de usuário: ";
	containerChamadaForcada.appendChild(texto).classList.add('row');

	let inputNomeUsuario = document.createElement('input');
	containerChamadaForcada.appendChild(inputNomeUsuario).classList.add('row');

	let divPai = document.createElement('div');
	containerChamadaForcada.appendChild(divPai).classList.add('row');


	let u_usuarios = [];
	c_usuario.carregar()['usuarios'].forEach(function(aux){
		u_usuarios.push({'nome':aux['nome'].toLowerCase(),'idusuario':aux['idusuario']});
	});
	
	inputNomeUsuario.addEventListener("keypress", function(e){
		if (e.keyCode == 13 && divPai.childElementCount == 1) {
			console.log("ainda vou implementar");
		}     
	});
	
	inputNomeUsuario.addEventListener("input", carregaAtividadesForcada);

	function carregaAtividadesForcada(){
		mataOsFilhos(divPai);
		console.log("to aqui no enter")

		
		if(inputNomeUsuario.value != "" ){
			
			u_usuarios.forEach(function(usuario){
				
				if(usuario['nome'].includes(inputNomeUsuario.value)){
				 	let item = document.createElement('div');
				 	item.innerText = usuario['nome'];
				 	item.id = usuario['idusuario'];

				 	divPai.appendChild(item).classList.add('tabelaChamadaForcada');


				 	item.onclick = function(){
				 		mataOsFilhos(containerChamadaForcada);

				 		var atividadesCadastradas;
				 		c_atividade.carregarForcado(item.id,function(e){
								atividadesCadastradas = JSON.parse(e);
								}, function(erro){
									if(erro == 409) alert("ERRO","ERRO","pastel-danger");
									else window.location.href = "./login.html";
									return true;
								});

				 		// console.log(atividadesCadastradas['atividades']);

				 		let headerChamadaForcada = document.createElement('h1');
						headerChamadaForcada.innerText = this.innerText;
						containerChamadaForcada.appendChild(headerChamadaForcada).classList.add('headerMain');

						let table=document.createElement('table');
						let linha=document.createElement('tr');
						let atividade=document.createElement('th');
						let presenca=document.createElement('th');

						table.appendChild(linha);
						linha.appendChild(atividade).innerText = "atividade";
						linha.appendChild(presenca).innerText = "presenca";

						containerChamadaForcada.appendChild(table).classList.add('tabelaPermissoes');

						let atividades = c_atividade.carregarAtividadesApresentador()['atividades'];
						
						atividades.forEach(function(aux){
					
							let linha=document.createElement('tr');
							let atividade=document.createElement('th');
							let presenca=document.createElement('th');

							atividade.id = aux['idatividade'];
							atividade.innerText = aux['titulo'];
							linha.appendChild(atividade);

							presenca.id = aux['idatividade'];
							presenca.innerText = "Inscrever-se";
							linha.appendChild(presenca);

							table.appendChild(linha);

							atividadesCadastradas['inscricoes'].forEach(function(linha){
								if(atividade.id == linha['idatividade']){
									jaTaInscrito(presenca);
								}
							});

							presenca.addEventListener('click', ()=>{
								
								c_usuario.inscricaoForcada(atividade.id,item.id, function(){
									jaTaInscrito(presenca,this);
								}, function(erro){
									if(erro == 409) alert("ERRO","Conflito de Horário ou Vagas esgotadas","pastel-danger");
									else window.location.href = "./login.html";
									return true;
								});
								

								c_atividade.fazerChamada(atividade.id, item.id, function(){
									// mudarPresenca(presenca);
								}, function(erro){
									if(erro == 406) alert("Erro ao fazer chamada");
									else window.location.href = "./login.html";
									return true;			
								});
							});
								
						});	
					}
				}			
				
			});
		}
	}
}

interfaceUsuario();


function isMobile(){
	if(window.innerWidth <= 759){
		return true;
	}
	return false;
}

// EVENTOS MENU
c_panel.listarAtividades.addEventListener("click", function(){
	mataOsFilhos(main);
	listarAtividades();
});

c_panel.btnInscreverAtividade.addEventListener("click", function(){
	mataOsFilhos(main);
	var listaApresentadores = c_usuario.carregarApresentadores()['apresentadores'];
	criarAtividades(listaApresentadores);
});

btnAutorizacoes.addEventListener("click", function(){
	mataOsFilhos(main);
	mudarPermissao();
});

btnChamada.addEventListener("click", function(){
	mataOsFilhos(main);
	chamada();
});

btnGerarCertificado.addEventListener("click", function(){
	mataOsFilhos(main);
	c_usuario.gerarCertificado();
});

btnEditarPerfil.addEventListener("click", function(){
	mataOsFilhos(main);
	alert("Em Desenvolvimento");
});

btnChamadaForcada.addEventListener("click", function(){
	mataOsFilhos(main);
	chamadaForcada();
});

btnRelatorios.addEventListener("click", function(){
	mataOsFilhos(main);
	relatorios();
});

btnSair.addEventListener("click", function(){
	window.location.href = "http://www.etic.ifc-camboriu.edu.br/2018/index.html";
});



//Codigo retirado da página https://www.sitepoint.com/get-url-parameters-with-javascript/
function getAllUrlParams(url) {

  // get query string from url (optional) or window
  var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

  // we'll store the parameters here
  var obj = {};

  // if query string exists
  if (queryString) {

    // stuff after # is not part of query string, so get rid of it
    queryString = queryString.split('#')[0];

    // split our query string into its component parts
    var arr = queryString.split('&');

    for (var i=0; i<arr.length; i++) {
      // separate the keys and the values
      var a = arr[i].split('=');

      // in case params look like: list[]=thing1&list[]=thing2
      var paramNum = undefined;
      var paramName = a[0].replace(/\[\d*\]/, function(v) {
        paramNum = v.slice(1,-1);
        return '';
      });

      // set parameter value (use 'true' if empty)
      var paramValue = typeof(a[1])==='undefined' ? true : a[1];

      // (optional) keep case consistent
      paramName = paramName.toLowerCase();
      paramValue = paramValue.toLowerCase();

      // if parameter name already exists
      if (obj[paramName]) {
        // convert value to array (if still string)
        if (typeof obj[paramName] === 'string') {
          obj[paramName] = [obj[paramName]];
        }
        // if no array index number specified...
        if (typeof paramNum === 'undefined') {
          // put the value on the end of the array
          obj[paramName].push(paramValue);
        }
        // if array index number specified...
        else {
          // put the value at that index number
          obj[paramName][paramNum] = paramValue;
        }
      }
      // if param name doesn't exist yet, set it
      else {
        obj[paramName] = paramValue;
      }
    }
  }

  return obj;
}

