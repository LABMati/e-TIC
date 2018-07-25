class Countdown{
    constructor(final){
        this.end = final
        this.actual = {
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0
        }
        this.timer
        this.setup()
    }

    setup(){
        this.timer = setInterval(()=>{
            this.getRemaing(this.end)
        }, 1000)
    }

    getRemaing(final){
        let timeRange = final - new Date().getTime()

        this.actual.days = Math.floor(timeRange / (1000 * 60 * 60 * 24));
        this.actual.hours = Math.floor((timeRange % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        this.actual.minutes = Math.floor((timeRange % (1000 * 60 * 60)) / (1000 * 60));
        this.actual.seconds = Math.floor((timeRange % (1000 * 60)) / 1000);

        if(this.timeRange < 0)
            clearInterval(this.timer)
    }
}