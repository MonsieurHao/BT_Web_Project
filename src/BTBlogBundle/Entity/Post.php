<?php

namespace BTBlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="BTBlogBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\ManyToOne(targetEntity="BTBlogBundle\Entity\Articles")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $articles;


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
     *
     * @ORM\Column(name="pseudo", type="string", length=50)
     * @Assert\Type("string")
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="commentary", type="text", length=255)
     * @Assert\Length(min=5, minMessage="This commentary must be 5 character long!" )
     */
    private $commentary;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Post
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date->format('Y-m-D H:i');
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     *
     * @return Post
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set commentary
     *
     * @param string $commentary
     *
     * @return Post
     */
    public function setCommentary($commentary)
    {
        $this->commentary = $commentary;

        return $this;
    }

    /**
     * Get commentary
     *
     * @return string
     */
    public function getCommentary()
    {
        return $this->commentary;
    }

    

    /**
     * Set articles
     *
     * @param \BTBlogBundle\Entity\Articles $articles
     *
     * @return Post
     */
    public function setArticles(\BTBlogBundle\Entity\Articles $articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Get articles
     *
     * @return \BTBlogBundle\Entity\Articles
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
