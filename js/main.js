let header = document.querySelector("header")
let aboutBut = document.querySelector("div.more")
let aboutSec = document.querySelector("section#about")
let steps = document.querySelectorAll(".step")
let carouselImg = document.querySelectorAll(".window")
let carousel = new Scroller(document.querySelector(".carousel"), 3)
let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
window.onload = addBg(carouselImg, ["img/0.jpg", "img/3.jpg", "img/5.jpg"])

window.addEventListener("scroll", ev=>{
    if(mediaQuery.matches){
        return
    }else{
        if(window.scrollY > 150)
            header.style.height = "15vh"
        else{
            header.style.height = '20vh'
        }
    }
})

function addBg(windows, imgs){
    for (let i = 0; i < windows.length; i++) {
        windows[i].style.background = "url("+imgs[i]+")"
    }    
}

steps.forEach(step =>{
    step.addEventListener('click',ev=>{
        carousel.slide(step.dataset.pos)
    })
})

setInterval(()=>{
    carousel.slide(carousel.current+2)
}, 5000)

about.addEventListener("click", ()=>{
    window.scrollTo("#about")
})