<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserController extends Controller
{

    /**
     * @Route("/user", name="user")
     */
    public function indexAction(Security $security, Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        //dump($user); die;
        $usuario = $security->getUser();

        $form = $this->createForm(UserType::class, $usuario);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // senha codificada
            $senhaCodificada = $passwordEncoder->encodePassword($usuario, $usuario->getPassword());
            $usuario->setSenha($senhaCodificada);

            // entity manager para salvar o usuário
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirect($this->generateUrl('noticia.destaque'));
        }

        return $this->render("user/user.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
