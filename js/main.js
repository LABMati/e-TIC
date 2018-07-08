let header = document.querySelector("header")
let aboutBut = document.querySelector("div.more")
let about = document.querySelectorAll("section#about div.about-item")
let steps = document.querySelectorAll(".step")
let carouselImg = document.querySelectorAll(".window")
let carousel = new Scroller(document.querySelector(".carousel"), 3)
let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
let parallax = new Poorallax("div.carousel", 10)

window.onload = function(){
    if(mediaQuery.matches){
        carouselImg[0].firstElementChild.src = "img/jpg/mobileCarousel1.jpg"  
        carouselImg[1].firstElementChild.src = "img/png/mobileCarousel2.png"  
        carouselImg[2].firstElementChild.src = "img/png/mobileCarousel3.png"  
        about[0].firstElementChild.src = "img/infografico/mobile/mobile-info-all.png"
        about[0].style.minHeight = '100vh'
        about[1].remove()
        about[2].remove()
        about[3].remove()
        about[4].remove()
        about[3].firstElementChild.src = ""
        about[3].firstElementChild.className = "infografico"
        about[3].lastElementChild.src = ""
        about[4].firstElementChild.src = ""
        about[4].lastElementChild.src = ""
    }
}

window.addEventListener("scroll", ev=>{
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
