class Schedule{
    constructor(eventsJson, container){
        this.events = eventsJson
        this.container = container
        this.timeline
    }

    setup(){
        this.createTimeline()
    }

    createTimeline(){
        let hours = ["08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00"]
        let timeline = document.createElement("DIV")
        let ulTimeline = document.createElement("UL")
        let liTimeline = []
        for (let i = 0; i < 15; i++) {
            liTimeline.push(document.createElement("LI"))
            let span = document.createElement("SPAN")
            span.innerText = hours[i]
            liTimeline[i].appendChild(span)
        }
        this.timeline = liTimeline;
    }

    createEvent(event, rootElement){
        let eventElement = document.createElement("LI")

        eventElement.classList.add("single-event")
        eventElement.dataset.start = event.start
        eventElement.dataset.end = event.end
        eventElement.dataset.content = event.content
        eventElement.dataset.event = event.event

        let link = document.createElement("A")
        link.href = "#"+event.number
        let em = document.createElement("EM")
        em.classList.add("event-name")
        em.innerText = event.name
        eventElement.appendChild(link)
        link.appendChild(em)
        rootElement.appendChild(eventElement)
    }

    assembleEvents(){
        let eventDiv = document.querySelector("DIV")
        eventDiv.classList.add("events")
        eventDiv.appendChild(document.createElement("UL"))

    }
}

let events = [
    {
        start: foo,
        end: foo,
        content: foo,
        event: foo,
        number: foo,
        name: foo
    }
]