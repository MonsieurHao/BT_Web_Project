<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 02/12/2017
 * Time: 13:04
 */

// src/BTBlogBundle/Entity/User.php

namespace BTBlogBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
}