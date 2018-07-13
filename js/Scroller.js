class Scroller {
    constructor(container, max) {
        this.container = container
        this.windows = container.querySelectorAll('.window')
        this.currentWindow = this.windows[0]
        this.current = 0
        this.toLeft = (container.scrollWidth > container.offsetWidth)
        this.steps = document.querySelectorAll("div.step")
        this.max = max-1
    }

    setup() {

    }

    // setTimer(time){
    //     setInterval(this.slide(this.current+2), time)
    // }

    slideTimes(step) {
        this.slide(this.current + step)
    }

    slide(step) {
        this.current = step - 1

        this.container.dispatchEvent(
            new CustomEvent("scroller-BEFORE", {
                detail: {
                    currentWindow: this.current
                }
            })
        )

        this.container.addEventListener("transitionend", () => {
            this.container.dispatchEvent(
                new CustomEvent("scroller-after", {
                    detail: {
                        currentWindow: this.current
                    }
                })
            )
        })

        if (this.current < 0 || this.current > this.max)
            this.current = 0

        if (this.windows.length - 1 <= this.current)
            this.current = this.windows.length - 1

        if (this.toLeft)
            return this.container.style.transform = `translate3d(${this.current * -100}%,0,0)`

        return this.container.style.transform = `translateY(${this.current * -100}%)`
    }

    next() {
        return this.slide(1)
    }

    prev() {
        return this.slide(-1)
    }
}

