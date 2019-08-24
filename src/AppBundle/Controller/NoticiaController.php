<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/noticia", name="noticia.")
 */
class NoticiaController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction() {
        return $this->render("noticia/index.html.twig", []);
    }
}
