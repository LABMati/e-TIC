class Report{
    constructor(option, success, error, content){
        this.option = option
        this.payload = content
        this.callback = success
        this.errorCallback = error
        this.response
        this.requisicao(this.option, this.callback, this.errorCallback, this.payload)
    }

    async requisicao(option, callback, errorCallback, content){
        const token = "&token=" + window.sessionStorage.getItem('token')
        const request = await fetch("http://www.etic.ifc-camboriu.edu.br/etic-2018/server/router.php?option=" + option + token, {
            method: "POST",
            cache: "no-cache",
            credentials: "same-origin",
            headers:{
                "Content-Type": "application/json; charset=utf-8",
            },
            body: JSON.stringify(content)
        })
        this.response = await request.json()
        if(await request.status === 200){
            callback(this.response)
        }else{
            errorCallback()
        }
    }
}