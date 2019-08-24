<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Noticia
 *
 * @ORM\Table(name="noticia")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoticiaRepository")
 */
class Noticia
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
     * @ORM\Column(name="titulo", type="string", length=255)
     */
    private $titulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="date")
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="resumo", type="string", length=255)
     */
    private $resumo;

    /**
     * @var string
     *
     * @ORM\Column(name="imagem", type="string", length=100)
     */
    private $imagem;

    /**
     * @var string
     *
     * @ORM\Column(name="conteudo", type="string", length=255)
     */
    private $conteudo;


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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Noticia
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     *
     * @return Noticia
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set resumo
     *
     * @param string $resumo
     *
     * @return Noticia
     */
    public function setResumo($resumo)
    {
        $this->resumo = $resumo;

        return $this;
    }

    /**
     * Get resumo
     *
     * @return string
     */
    public function getResumo()
    {
        return $this->resumo;
    }

    /**
     * Set imagem
     *
     * @param string $imagem
     *
     * @return Noticia
     */
    public function setImagem($imagem)
    {
        $this->imagem = $imagem;

        return $this;
    }

    /**
     * Get imagem
     *
     * @return string
     */
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     * Set conteudo
     *
     * @param string $conteudo
     *
     * @return Noticia
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;

        return $this;
    }

    /**
     * Get conteudo
     *
     * @return string
     */
    public function getConteudo()
    {
        return $this->conteudo;
    }
}

