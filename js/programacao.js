// let carousel = new Scroller(document.querySelector("div.carousel"), 5)
let steps = document.querySelectorAll(".step")
let mediaQuery = window.matchMedia("(max-aspect-ratio: 1/1)")

if(mediaQuery.matches){
    let i = 27
    for (let step of steps) {
        step.innerText = i
        i++
    }
}

steps.forEach(step =>{
    step.addEventListener('click',ev=>{
        carousel.slide(step.dataset.pos)
    })
})

const velo = 20;  
   var posx = 200;
   var posy = 200; 
   var sizex = sizey = 10;
   var applex = appley = Math.floor(Math.random()*400);
   var canvas, ctx;
   
  
 
function start(){
  
    canvas = document.createElement('canvas');
    ctx = canvas.getContext('2d');
    canvas.width = 400;
    canvas.height = 400;
    canvas.style.background = 'black';
    document.body.appendChild(canvas);
 

    ctx.fillStyle='white'
    ctx.fillRect(posx, posy, sizex, sizey)
    
}

start()

// Esse evento não to funcionando, tenta usar windowao invés de document
document.addEventListener('load', start)


const velo = 20;  
var posx = 200;
var posy = 200; 
var sizex = sizey = 10;
var applex = appley = Math.floor(Math.random()*400);
var canvas, ctx;
   
  
 
function start(){
  
    canvas = document.createElement('canvas');
    ctx = canvas.getContext('2d');
    canvas.width = 400;
    canvas.height = 400;
    canvas.style.backgroundColor = 'black';
    ctx.fillStyle='red'
    ctx.fillRect(posx, posy, sizex, sizey)
    
}


window.addEventListener("load", start)