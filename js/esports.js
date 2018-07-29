// let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
// let header = document.querySelector("header")
// let mobileheader = document.querySelector("nav.mobile")
let overlay = document.querySelector(".video-overlay")
let video = document.querySelector("video")

video.play()

// window.addEventListener("scroll", ev=>{
//     if(window.scrollY < innerHeight){
//         header.style.opacity = "0"
//         header.style.zIndex = '-4'
//     }
//     else{
//         header.style.opacity = '1'
//         header.style.zIndex = '2'
//     }
// })



setTimeout(()=>overlay.style.height = video.offsetHeight + 'px', 1000)