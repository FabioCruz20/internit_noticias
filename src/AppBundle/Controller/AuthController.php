<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use AppBundle\Form\AdminType;
use AppBundle\Entity\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends Controller
{
    /**
     * @Route("/", name="login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils) {

        // pegando o possível erro de login
        $erro = $authenticationUtils->getLastAuthenticationError();

        // pegando último email digitado
        $ultimoEmail = $authenticationUtils->getLastUsername();

        return $this->render("auth/login.html.twig", [
            "ultimo_email" => $ultimoEmail,
            "erro" => $erro
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder) {

        // cria usuário e formulário.
        $usuario = new User();
        $form = $this->createForm(UserType::class, $usuario);

        // manipular submissão de formulário
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // codificar senha
            $senhaCodificada = $passwordEncoder->encodePassword($usuario, $usuario->getPassword());
            $usuario->setSenha($senhaCodificada);

            // entity manager para salvar o usuário
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirect($this->generateUrl('login'));
        }

        return $this->render("auth/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logoutAction() {
        return $this->redirect($this->generateUrl("login"));
    }

    /**
     * @Route("/admin", name="admin.login")
     */
    public function adminLoginAction(AuthenticationUtils $authenticationUtils) {

        // pegando o possível erro de login
        $erro = $authenticationUtils->getLastAuthenticationError();

        // pegando último email digitado
        $ultimoEmail = $authenticationUtils->getLastUsername();

        return $this->render("admin/login.html.twig", [
            "ultimo_email" => $ultimoEmail,
            "erro" => $erro
        ]);
    }

    /**
     * @Route("/admin/register", name="admin.register")
     */
    public function adminRegisterAction(Request $request, 
        UserPasswordEncoderInterface $passwordEncoder) 
    {
        // cria usuário e formulário.
        $usuario = new Admin();
        $form = $this->createForm(AdminType::class, $usuario);

        // manipular submissão de formulário
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // codificar senha
            $senhaCodificada = $passwordEncoder->encodePassword($usuario, $usuario->getPassword());
            $usuario->setSenha($senhaCodificada);

            // entity manager para salvar o usuário
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirect($this->generateUrl('admin.login'));
        }

        return $this->render("admin/register.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
