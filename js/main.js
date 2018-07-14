// Used for header manipulation
let header = document.querySelector("header")

let about = document.querySelectorAll("section#about div.about-item")
let steps = document.querySelectorAll(".step")
let carouselImg = document.querySelectorAll(".window")
let carousel = new Scroller(document.querySelector(".carousel"), 3)
let carousel2 = new Scroller(document.querySelector(".carousel"), 3)

// Everything timer needs to work
let timer = new Countdown(new Date('08/27/2018').getTime())
let timerContainers = document.querySelectorAll("div.timer")

    
let interval = setInterval(()=>{
    timerContainers[0].firstElementChild.innerText = timer.actual.days 
    timerContainers[1].firstElementChild.innerText = timer.actual.hours
    timerContainers[2].firstElementChild.innerText = timer.actual.minutes
    timerContainers[3].firstElementChild.innerText = timer.actual.seconds
}, 1000)

window.onload = function(){
    let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
    if(mediaQuery.matches){
        carouselImg[0].firstElementChild.src = "img/png/mobileCarousel1-min.png"  
        carouselImg[1].firstElementChild.src = "img/png/mobileCarousel2-min.png"  
        carouselImg[2].firstElementChild.src = "img/png/mobileCarousel3-min.png"  
        about[0].firstElementChild.src = "img/infografico/mobile/mobile-info-all-min.png"
        about[0].style.minHeight = '100vh'
        about[1].remove()
        about[2].remove()
        about[3].remove()
        about[4].remove()
    }
}

window.addEventListener("scroll", ev=>{
    let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
    for (const aboutItem of about) {
        if(scrollY + window.innerHeight >= aboutItem.offsetTop)
            aboutItem.style.opacity = "1"
        else{
            aboutItem.style.opacity = "0"
        }
    }
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

steps.forEach(step =>{
    step.addEventListener('click',ev=>{
        carousel.slide(step.dataset.pos)
        clearInterval(carouselInterval)    
        carouselInterval = setInterval(() => {
            carousel.slide(carousel.current + 2)
        }, 5000)
    })
})

let carouselInterval = setInterval(() => {
    carousel.slide(carousel.current + 2)
}, 5000)