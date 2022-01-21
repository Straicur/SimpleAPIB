<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class PostSuccessModel{
    /**
     * @var integer
     *
     * @SWG\Property(type="integer", description="Token");
     */
    public $post_id;
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="title");
     */
    public $title;
    /**
     * @var string
     *
     * @SWG\Property(type="string", description="text");
     */
    public $text;
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="post_date");
     */
    public $post_date;
    /**
     * @var array
     *
     * @SWG\Property(type="array", description="comments");
     */
    public $comments ;

    function __construct(int $post_id,string $title,string $text, string $post_date,array $comments)
    {
        $this->post_id = $post_id;
        $this->title = $title;
        $this->text = $text;
        $this->post_date = $post_date;
        $this->comments = $comments;
    }
}