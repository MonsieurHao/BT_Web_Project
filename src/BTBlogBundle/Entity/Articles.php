<?php

namespace BTBlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Articles
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="BTBlogBundle\Repository\ArticlesRepository")
 * @UniqueEntity(fields="title", message="An article already exist with this title!")
 */
class Articles
{
    /**
     * @ORM\OneToMany(targetEntity="BTBlogBundle\Entity\Media", mappedBy="articles")
     */
    private $medias;

    /**
     *@ORM\OneToMany(targetEntity="BTBlogBundle\Entity\Post", mappedBy="articles")
     */
    private $posts;

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
     * @ORM\Column(name="title", type="string", length=100, unique=true)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\Type("string")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="postBy", type="string", length=1)
     * @Assert\Length(min=1,max=1,minMessage="Please add a character!",maxMessage="Only one character!")
     */
    private $postBy;

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
     * Set title
     *
     * @param string $title
     *
     * @return Articles
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Articles
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
        return $this->date->format('H:i:s j-M-Y');
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Articles
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set postBy
     *
     * @param string $postBy
     *
     * @return Articles
     */
    public function setPostBy($postBy)
    {
        $this->postBy = $postBy;

        return $this;
    }

    /**
     * Get postBy
     *
     * @return string
     */
    public function getPostBy()
    {
        return $this->postBy;
    }

    /**
     * Add media
     *
     * @param \BTBlogBundle\Entity\Media $media
     *
     * @return Articles
     */
    public function addMedia(\BTBlogBundle\Entity\Media $media)
    {
        $this->medias[] = $media;

        $media->setArticles($this);

        return $this;
    }

    /**
     * Remove media
     *
     * @param \BTBlogBundle\Entity\Media $media
     */
    public function removeMedia(\BTBlogBundle\Entity\Media $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Add post
     *
     * @param \BTBlogBundle\Entity\Post $post
     *
     * @return Articles
     */
    public function addPost(\BTBlogBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        $post->setArticles($this);

        return $this;
    }

    /**
     * Remove post
     *
     * @param \BTBlogBundle\Entity\Post $post
     */
    public function removePost(\BTBlogBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
