<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\NamedQuery;
use Doctrine\ORM\Mapping\NamedQueries;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="Comment")
 */
class Comment
{
    function __construct($post_id,$user_id,$text) {
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->text = $text;
    }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="comment_id", type="integer", nullable=false)
     */
    private $comment_id;

    /**
     * @ORM\ManyToOne(targetEntity="Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="post_id")
     */
    private $post_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user_id;
    /**
     * @ORM\Column(name="text",type="string", length=180, nullable=false)
     */
    private $text;

    public function getComment_id(): ?int
    {
        return $this->comment_id;
    }
    public function getUser_id(): ?User
    {
        return $this->user_id;
    }

    public function setUser_id(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
    public function getPost_id(): ?Post
    {
        return $this->post_id;
    }

    public function setPost_id(?Post $post_id): self
    {
        $this->post_id = $post_id;

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

}
