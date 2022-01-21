<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\NamedQuery;
use Doctrine\ORM\Mapping\NamedQueries;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="Post")
 */
class Post
{
    function __construct($admin_id,$title,$text) {
        $this->admin_id = $admin_id;
        $this->title = $title;
        $this->text = $text;
        $this->post_date = new DateTime('NOW');
    }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="post_id", type="integer", nullable=false)
     */
    private $post_id;
    /**
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="admin_id")
     */
    private $admin_id;
    /**
     * @ORM\Column(name="title",type="string", length=180, nullable=false)
     */
    private $title;
    /**
     * @ORM\Column(name="text",type="string", length=180, nullable=false)
     */
    private $text;
    /**
     * @ORM\Column(name="post_date", type="datetime", nullable=false)
     */
    private $post_date;


    public function getPost_id(): ?int
    {
        return $this->post_id;
    }
    public function getUserId(): ?AdminUser
    {
        return $this->admin_id;
    }

    public function setUserId(?AdminUser $admin_id): self
    {
        $this->admin_id = $admin_id;

        return $this;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
    public function getPost_date()
    {
        return $this->post_date;
    }

    public function setPost_date(): self
    {
        $this->post_date =  new DateTime('NOW');

        return $this;
    }


}
