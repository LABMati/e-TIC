let mobileNavBt = document.querySelector("nav.mobile div.nav-header button")
let mobileNavMenu = document.querySelector("nav.mobile div.nav-menu")


mobileNavBt.addEventListener('click', ev=>{
    if(mobileNavMenu.offsetHeight == 0)
        mobileNavMenu.style.height = "30vh"
    else
        mobileNavMenu.style.height = "0"
})