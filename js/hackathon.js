let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
let steps = document.querySelectorAll(".step")
let header = document.querySelector("header")

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