class Poorallax{
    constructor(query,speed) {
        this.elements = document.querySelectorAll(query)
        speed *= .1
        if (speed > 1)
            speed = 1
        if (speed < 0)
            speed = 0
        this.speed = speed
        this.elements.forEach(el => this._setEffect(el))
    }
    _setEffect(el) {
        window.addEventListener('scroll',() => {
            let deslocY = window.scrollY + window.innerHeight
            if (deslocY > el.offsetTop && window.scrollY <= el.offsetTop + el.offsetHeight)
                el.style.backgroundPositionY = `${((window.scrollY - el.offsetTop) * this.speed)}px`
        })
    }
}