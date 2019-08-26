function userDelete(id) {
    const resposta = confirm("Tem certeza que deseja apagar este assinante?");

    if (resposta) {
        window.location.replace(`/admin/user/delete/${id}`);
    }
}