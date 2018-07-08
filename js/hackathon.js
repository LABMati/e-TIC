let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
let steps = document.querySelectorAll(".step")
let header = document.querySelector("header")
let aboutBut = document.querySelector("div.more")
let about = document.querySelectorAll("section#about div.about-item")

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