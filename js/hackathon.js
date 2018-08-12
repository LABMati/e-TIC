let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
let steps = document.querySelectorAll(".step")
let header = document.querySelector("header")
<<<<<<< HEAD
=======
let aboutBut = document.querySelector("div.more")
let about = document.querySelectorAll("section#about div.about-item")
//let carousel = new Scroller(document.querySelector(".carousel"), 3)
>>>>>>> 956c52735b57089ef544be9d2e84bc633df517f5

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