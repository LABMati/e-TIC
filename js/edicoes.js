// let header = document.querySelector("header")
let aboutBut = document.querySelector("div.more")
let about = document.querySelectorAll("section#about div.about-item")
let steps = document.querySelectorAll(".step")
let carouselImg = document.querySelectorAll(".window")
let carousel = new Scroller(document.querySelector(".carousel"), 3)
let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")

window.onload = function () {
    if (mediaQuery.matches) {
        carouselImg[0].firstElementChild.src = "img/e-TIC2017/scroller/mobile/1.jpg"
        carouselImg[1].firstElementChild.src = "img/e-TIC2017/scroller/mobile/2.jpg"
        carouselImg[2].firstElementChild.src = "img/e-TIC2017/scroller/mobile/3.jpg"
    }
}

steps.forEach(step => {
    step.addEventListener('click', () => {
        carousel.slide(step.dataset.pos)
    })
})

setInterval(() => {
    carousel.slide(carousel.current + 2)
}, 5000)