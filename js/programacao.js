let carousel = new Scroller(document.querySelector("div.carousel"), 5)
let steps = document.querySelectorAll(".step")
steps.forEach(step =>{
    step.addEventListener('click',ev=>{
        carousel.slide(step.dataset.pos)
    })
})