<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Admin
 *
 * @ORM\Table(name="admin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdminRepository")
 */
class Admin implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="senha", type="string", length=255)
     */
    private $senha;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Admin
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set senha
     *
     * @param string $senha
     *
     * @return Admin
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get senha
     *
     * @return string
     */
    public function getSenha()
    {
        return $this->senha;
    }

    // métodos da interface UserInterface
    public function getRoles() 
    {
        return ['ROLE_USER'];
    }

    public function getPassword() 
    {
        return $this->senha;
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() 
    {
        return $this->email;
    }

    public function eraseCredentials() {

    }
    // fim dos métodos da interface UserInterface

    // métodos da interface Serializable
    public function serialize() {
        return serialize([
            $this->id, 
            $this->email,
            $this->senha
        ]);
    }

    public function unserialize($serialized) {
        list(
            $this->id,
            $this->email,
            $this->senha
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}

