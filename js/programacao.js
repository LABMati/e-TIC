let eventTriggers = document.querySelectorAll("li.single-event")
let eventos 
let url = "http://www.etic.ifc-camboriu.edu.br/2018/"
let timetables = document.querySelectorAll("div.cd-schedule")
let timetableTriggers = document.querySelectorAll("button.day")

async function getEvents(){
    let response = await fetch(url+"json/getJson.php?path=eventos");
    eventos = await response.json()
    attachEvents()
}

for (let i = 0; i < timetableTriggers.length; i++) {
    timetableTriggers[i].addEventListener("click", ()=>{
        hideAll(timetables)
        timetables[i].classList.remove('hidden')
    })
}

function hideAll(el){
    for (let e of el) {
        e.classList.add("hidden")
    }
}

window.addEventListener("load", ()=>{
    getEvents()
})

getEvents()

function attachEvents(){
    for (let i = 0; i < eventTriggers.length; i++) {
        eventTriggers[i].addEventListener("click", (ev)=>{
            let eventId = ev.target.closest("li.single-event").dataset.event
            swal({
                title: eventos[(eventId - 1)].nome,
                html: '<h3>' + eventos[(eventId - 1)].palestrante + '</h3> <br> <h2 class="toggleHide" id="tog' +(eventId - 1)+ '"> Mais informações </h2>' + '<div class="hide" id=div' + (eventId - 1)+ '>' + eventos[(eventId - 1)].descricao +'</div>', 
                footer: eventos[(eventId - 1)].local + ' - ' + eventos[(eventId - 1)].inicio + ' : ' + eventos[(eventId - 1)].final
              })
              
            document.querySelector("h2#tog"+(eventId - 1)).addEventListener("click", ()=>{
                let div = document.querySelector("div#div"+(eventId - 1))
                div.style.display = 'block'
                document.querySelector("h2#tog"+(eventId - 1)).style.display = 'none'
            })
        })
    }
}
