<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Noticia;
use AppBundle\Form\NoticiaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NoticiaController extends Controller
{
    /**
     * @Route("/noticia", name="noticia.destaque")
     */
    public function indexAction() {

        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticias = $repository->findBy(
            ["destaque" => true]
        );

        return $this->render("noticia/index.html.twig", [
            "noticias" => $noticias
        ]);
    }

    /**
     * @Route("/noticia/todas", name="noticia.todas")
     */
    public function todasAction() {
        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticias = $repository->findAll();

        return $this->render("noticia/index.html.twig", [
            "noticias" => $noticias
        ]);
    }

    /**
     * @Route("/noticia/{id}", name="noticia.detalhe")
     */
    public function detalheNoticiaAction($id) {
        $repository = $this->getDoctrine()->getRepository(Noticia::class);

        $noticia = $repository->findById($id);

        return $this->render("noticia/noticia.html.twig", [
            "noticia" => $noticia[0]
        ]);
    }

    /**
     * @Route("/admin/noticia", name="noticia.admin")
     */
    public function noticiaAdminAction() {

        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticias = $repository->findAll();

        return $this->render("admin/noticia.html.twig", [
            "noticias" => $noticias
        ]);
    }

    /**
     * @Route("/admin/noticia/{id}", name="noticia.edit")
     */
    public function noticiaEditAction(Noticia $noticia, Request $request) {

        //dump($noticia); die;

        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $form = $this->createForm(NoticiaType::class, $noticia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $destaques = $repository->findBy(['destaque' => 1]);
            $qtdDestaques = count($destaques);

            /** @var UploadedFile $imagem */
            $imagem = $form["imagem"]->getData();

            if ($imagem) {
                // gerando um nome único para o arquivo de imagem, também
                // removendo acentuações e espaços em branco.
                $nomeOriginal = pathinfo($imagem->getClientOriginalName(), PATHINFO_FILENAME);
                $nomeSeguro = transliterator_transliterate(
                    "Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()",
                    $nomeOriginal
                );
                $nomeNovo = "$nomeSeguro-". uniqid() .".". $imagem->guessExtension();
                
                // move o arquivo para o diretório web/uploads/imagens/
                $imagem->move(
                    $this->getParameter('uploads_dir'),
                    $nomeNovo
                );

                // atribui o caminho da nova imagem ao objeto notícia
                $noticia->setImagem($nomeNovo);
            }
            // garantindo que só pode haver 3 notícias em destaque
            //$noticia->setDestaque($noticia->getDestaque() && $qtdDestaques < 3);
            if ($noticia->getDestaque()) {
                if ($qtdDestaques >= 3) {
                    $this->addFlash("notice", "Não é possível marcar mais de 3 notícias como destaque.");
                    $noticia->setDestaque(false);
                }
            }

            // persistindo a notícia no banco de dados
            $em = $this->getDoctrine()->getManager();
            $em->persist($noticia);
            $em->flush();

            return $this->redirect($this->generateUrl('noticia.admin'));
        }

        return $this->render("admin/noticia-edit.html.twig", [
            "form" => $form->createView(),
            "funcao" => "Atualizar"
        ]);
    }

    /**
     * @Route("admin/noticia/delete/{id}", name="noticia.delete")
     */
    public function noticiaDeleteAction(Noticia $noticia) {

        // referenciando o entity manager para deletar a notícia
        $em = $this->getDoctrine()->getManager();
        $em->remove($noticia);
        $em->flush();

        $this->addFlash("success", "Notícia apagada com sucesso.");

        return $this->redirect($this->generateUrl("noticia.admin"));
    }

    /**
     * @Route("admin/noticia/create/", name="noticia.criar")
     */
    public function noticiaCreateAction(Request $request) {

        // objeto notícia a ser preenchido
        $noticia = new Noticia();
        $form = $this->createForm(NoticiaType::class, $noticia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imagem = $form["imagem"]->getData();
            // armazena a imagem e salva a url na notícia.
            $novoNome = $this->armazenaImagem($imagem);
            $noticia->setImagem($novoNome);

            // verifica se a notícia pode ficar em destaque ou não.
            $this->avaliaDestaque($noticia);

            // persistindo a notícia no banco de dados
            $em = $this->getDoctrine()->getManager();
            $em->persist($noticia);
            $em->flush();

            return $this->redirect($this->generateUrl("noticia.admin"));
        }

        return $this->render("admin/noticia-edit.html.twig", [
            "form" => $form->createView(),
            "funcao" => "Criar"
        ]);
    }

    /**
     * Armazena imagem no diretório web/uploads/imagens 
     * com um nome único.
     * @param UploadedFile $imagem objeto imagem que será movido
     * @return string nome novo do arquivo movido
     */
    private function armazenaImagem(UploadedFile $imagem) {
        if ($imagem) {
            // gerando um nome único para o arquivo de imagem, também
            // removendo acentuações e espaços em branco.
            $nomeOriginal = pathinfo($imagem->getClientOriginalName(), PATHINFO_FILENAME);
            $nomeSeguro = transliterator_transliterate(
                "Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()",
                $nomeOriginal
            );
            $nomeNovo = "$nomeSeguro-". uniqid() .".". $imagem->guessExtension();
            
            // move o arquivo para o diretório web/uploads/imagens/
            $imagem->move(
                $this->getParameter('uploads_dir'),
                $nomeNovo
            );

            return $nomeNovo;
        }
        return "";
    }

    /**
     * Avalia se a notícia pode ser destaque.
     * A condição é que não haja mais que 3 notícias em destaque
     * ao mesmo tempo. Caso não seja possível, emite um aviso e configura
     * a propriedade destaque como false.
     * @param Noticia $noticia notícia a ser avaliada.
     */
    private function avaliaDestaque(Noticia $noticia) {
        // garantindo que só pode haver 3 notícias em destaque
        if ($noticia->getDestaque()) {

            $repository = $this->getDoctrine()->getRepository(Noticia::class);
            $qtdDestaques = count($repository->findBy(["destaque" => 1]));

            if ($qtdDestaques >= 3) {

                $this->addFlash("notice", "Não é possível marcar mais de 3 notícias como destaque.");
                $noticia->setDestaque(false);
            }
        }
    }
}
