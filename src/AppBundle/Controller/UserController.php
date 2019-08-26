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
    public function indexAction(Security $security, Request $request, 
        UserPasswordEncoderInterface $passwordEncoder) 
    {
        //dump($user); die;
        $usuario = $security->getUser();

        return $this->register($request, $passwordEncoder, $usuario, "noticia.destaque", 
            "user/user.html.twig", "Criar", "Usuário criado");
    }

    /**
     * @Route("/admin/user", name="user.admin")
     */
    public function userListAction() {

        $repository = $this->getDoctrine()->getRepository(User::class);

        $usuarios = $repository->findAll();

        return $this->render("admin/user.html.twig", [
            "usuarios" => $usuarios
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", name="user.edit")
     */
    public function userEditAction(User $usuario, Request $request, 
        UserPasswordEncoderInterface $passwordEncoder) 
    {
        return $this->register($request, $passwordEncoder, $usuario, "user.admin", 
            "admin/user-edit.html.twig", "Atualizar", "Usuário atualizado");
    }

    /**
     * @Route("admin/user/create", name="user.create")
     */
    public function userCreateAction(Request $request, UserPasswordEncoderInterface $passwordEncoder) {

        $usuario = new User();

        return $this->register($request, $passwordEncoder, $usuario, "user.admin", 
            "admin/user-edit.html.twig", "Criar", "Usuário criado");
    }


    private function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,
        User $usuario, $nomeRota, $urlForm, $funcao, $msgSucesso) 
    {

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

            $this->addFlash("success", $msgSucesso);

            return $this->redirect($this->generateUrl($nomeRota));
        }

        return $this->render($urlForm, [
            "form" => $form->createView(),
            "funcao" => $funcao
        ]);
    }
}
