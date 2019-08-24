<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Noticia;

/**
 * @Route("/noticia", name="noticia.")
 */
class NoticiaController extends Controller
{
    /**
     * @Route("/", name="destaque")
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
     * @Route("/todas", name="todas")
     */
    public function todasAction() {
        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticias = $repository->findAll();

        return $this->render("noticia/index.html.twig", [
            "noticias" => $noticias
        ]);
    }

    /**
     * @Route("/{id}", name="detalhe")
     */
    public function detalheNoticiaAction($id) {
        $repository = $this->getDoctrine()->getRepository(Noticia::class);

        $noticia = $repository->findById($id);

        return $this->render("noticia/noticia.html.twig", [
            "noticia" => $noticia
        ]);
    }
}
