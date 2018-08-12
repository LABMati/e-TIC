<<<<<<< HEAD
let eventTriggers = document.querySelectorAll("li.single-event")
let eventos 
let url = "http://etic.ifc-camboriu.edu.br/2018/"
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
=======
// let carousel = new Scroller(document.querySelector("div.carousel"), 5)
let eventTriggers = document.querySelectorAll("li.single-event")

let eventos 
let url = "200.135.34.151"

let response = await fetch(url+"/2018/eventos.json");
eventos = await response.json()

for (let i = 0; i < eventTriggers.length; i++) {
    eventTriggers[i].addEventListener("click", (ev)=>{
        let eventId = ev.target.closest("li.single-event").dataset.event
        swal({
            title: eventos[eventId].nome,
            html: '<h3>' + eventos[eventId].palestrante + '</h3> <br> <h2 class="toggleHide" id="tog' +eventId+ '"> Mais informações </h2>' + '<div class="hide" id=div' + eventId+ '>' + eventos[eventId].descricao +'</div>', 
            footer: eventos[eventId].local + ' - ' + eventos[eventId].inicio + ' : ' + eventos[eventId].final
          })
        if(eventTriggers[i].descricao){
            document.querySelector("h2#tog"+eventId).addEventListener("click", ()=>{
                let div = document.querySelector("div#div"+eventId)
                div.style.display = 'block'
                document.querySelector("h2#tog"+eventId).style.display = 'none'
            })
        }
    })
}
>>>>>>> 956c52735b57089ef544be9d2e84bc633df517f5
