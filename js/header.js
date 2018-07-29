let header = document.querySelector("header")
window.addEventListener("scroll", ev => {
    let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")
    if (mediaQuery.matches) {

        return
    } else {
        if (window.scrollY > 150)
            header.style.height = "12vh"
        else {
            header.style.height = '16vh'
        }
    }
})