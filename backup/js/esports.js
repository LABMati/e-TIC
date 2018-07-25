const lol = document.querySelector("div.slide-lol")
const csgo = document.querySelector("img.cszinho")

window.addEventListener("scroll", ev=>{
    console.log("ae")
    if(scrollY > 200){
        console.log("ae")
        slideEsports()
    }
})

function slideEsports(){
    lol.style.left = "10vw"
    csgo.style.left = "70vw"
}