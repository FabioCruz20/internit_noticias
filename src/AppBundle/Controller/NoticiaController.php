<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Noticia;
use AppBundle\Form\NoticiaType;
use Symfony\Component\HttpFoundation\Request;

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
            "form" => $form->createView()
        ]);
    }
}
