let carousel = new Scroller(document.querySelector("div.carousel"), 5)
let steps = document.querySelectorAll(".step")
let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")

if(mediaQuery.matches){
    let i = 27
    for (let step of steps) {
        step.innerText = i
        i++
    }
}

steps.forEach(step =>{
    step.addEventListener('click',ev=>{
        carousel.slide(step.dataset.pos)
    })
})