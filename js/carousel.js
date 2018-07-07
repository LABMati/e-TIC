class Carousel {
    constructor(container, direction){
        //container usado para o carousel
        this.container = container

        
        //array de imagens utilizadas
        this.imgs = container.querySelectorAll("img")
        this.imgContainer = {}

        this.setImageDiv()
        this.placeImg()
        
        //botao de retornar
        this.prevButton = {}        
        //botao de avancar 
        this.nextButton = {}

        //array de seletores de slides
        this.selectors = []
        

        this.current = 0
    }

    setImageDiv(){
        //cria container para as imagens
        let imgContainer = document.createElement("div")
        imgContainer.classname = 'img-container'
    
        //move todas as imagens para dentro do container
        this.imgs.forEach(img => {
            imgContainer.appendChild(img);
        });

        //retorna container de imagens
        this.imgContainer =  imgContainer
        this.container.appendChild(this.imgContainer)
    }
    
    placeImg(){
        //arruma estilo do container
        this.imgContainer.style.display = "flex"
        this.imgContainer.style.overflow = "hidden"
        this.imgContainer.style.flexGrow = "19"

        
        // this.imgs.forEach(img => {
        //     img.width = "100%"
        //     img.height = "100%"
        // });

        
    }

    setControlersDiv(){
        //cria container para os controladores
        let controlerDiv = document.createElement("div")
        controlerDiv.className = "controler-container"
        
        //cria botão de retornar
        let prevButton = document.createElement("span")
        prevButton.innerText = "<"
        prevButton.className = "slide-button"
        
        //cria botão de avancar
        let nextButton = document.createElement("span")
        nextButton.innerText = ">"
        nextButton.className = "slide-button"
        
        //cria container para os botões
        let btDiv = document.createElement("div")
        btDiv.appendChild(prevButton)
        btDiv.appendChild(nextButton)

        //coloca estilo para os botões
        btDiv.style.display = "flex"
        btDiv.style.justifyItems = "space-between"
        prevButton.style.height = "30%"
        nextButton.style.height = "30%"
         
        //arruma estilo do container
        

        this.prevButton = prevButton
        this.nextButton = nextButton
    }

    slide(position){
        if(position > this.windows.length-1)
            position = 0
        
        if(position<0)
            position = this.windows.length-1
        
        position  *= -1

        this.current = position
    }

    nextSlide(){
        this.slide(++this.current)
    }

    prevSlide(){
        this.slide(--this.current)
        
    }
}