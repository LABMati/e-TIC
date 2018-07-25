let form = document.querySelector('form')
VMasker(form.querySelector('input[name="telefone"]')).maskPattern("(99) 99999-9999");
form.querySelector('button').addEventListener("click", ev => {
    sendEmail()
    ev.preventDefault()
})
async function sendEmail() {
    let body = await JSON.stringify({
        "name": form.querySelector('input[name="nome"]').value,
        "email": form.querySelector('input[name="email"]').value,
        "subject": form.querySelector('input[name="assunto"]').value,
        "message": form.querySelector('textarea[name="mensagem"]').value,
        "phone": form.querySelector('input[name="telefone"]').value
    })
    let response = await fetch('http://127.0.0.1/contact_me.php', {
        method: 'POST',
        body: body
    });
    let values = await response.json()
    if (!values.result)
        swal("Erro!", values.body, "error");
    else
        swal("Obrigado!", '', "success");
}