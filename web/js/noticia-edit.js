function noticiaDelete(id) {

    const resposta = confirm("Tem certeza que deseja apagar esta notícia?");

    if (resposta) {
        window.location.replace(`/admin/noticia/delete/${id}`);
    }
}