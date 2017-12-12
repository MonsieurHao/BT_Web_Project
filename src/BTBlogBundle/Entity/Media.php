<?php

namespace BTBlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="BTBlogBundle\Repository\MediaRepository")
 */
class Media
{

    /**
     * @ORM\ManyToOne(targetEntity="BTBlogBundle\Entity\Articles", inversedBy="medias", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=5)
     * @Assert\Length(min=5,max=5,minMessage="Must be : video, music or image",maxMessage="Must be : video, music or image")
     */
    private $type;


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
     * @return Media
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date->format('Y-m-D H:i');
    }
    

    /**
     * Set link
     *
     * @param string $link
     *
     * @return Media
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Media
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set articles
     *
     * @param \BTBlogBundle\Entity\Articles $articles
     *
     * @return Media
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
