let eventTriggers = document.querySelectorAll("li.single-event")
let eventos 
let url = "http://etic.ifc-camboriu.edu.br/2018/"

async function getEvents(){

    let response = await fetch(url+"json/getJson.php?path=eventos");
    console.log("carregou json")
    eventos = await response.json()
    attachEvents()
}

window.addEventListener("load", ()=>{
    getEvents()
})

getEvents()

function attachEvents(){
    console.log("criou eventos")
    for (let i = 0; i < eventTriggers.length; i++) {
        eventTriggers[i].addEventListener("click", (ev)=>{
            let eventId = ev.target.closest("li.single-event").dataset.event
            swal({
                title: eventos[eventId].nome,
                html: '<h3>' + eventos[eventId].palestrante + '</h3> <br> <h2 class="toggleHide" id="tog' +eventId+ '"> Mais informações </h2>' + '<div class="hide" id=div' + eventId+ '>' + eventos[eventId].descricao +'</div>', 
                footer: eventos[eventId].local + ' - ' + eventos[eventId].inicio + ' : ' + eventos[eventId].final
              })
            if(eventos[i].descricao){
                document.querySelector("h2#tog"+eventId).addEventListener("click", ()=>{
                    let div = document.querySelector("div#div"+eventId)
                    div.style.display = 'block'
                    document.querySelector("h2#tog"+eventId).style.display = 'none'
                })
            }
        })
    }
}
