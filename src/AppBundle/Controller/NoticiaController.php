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
     * @Route("/", name="index")
     */
    public function indexAction() {

        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticias = $repository->findBy(
            ['destaque' => true]
        );

        return $this->render("noticia/index.html.twig", [
            'noticias' => $noticias
        ]);
    }
}
