<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class GetCommentModel{
    /**
     * @var integer
     *
     * @SWG\Property(type="integer", description="id");
     */
    public $comment_id;
    /**
     * @var integer
     *
     * @SWG\Property(type="integer", description="id");
     */
    public $user_id;
    /**
     * @var string
     *
     * @SWG\Property(type="string", description="text");
     */
    public $text;
    /**
     * @var boolean
     *
     * @SWG\Property(type="boolean", description="his");
     */
    public $his;
    function __construct(int $comment_id,int $user_id,string $text,bool $his)
    {
        $this->comment_id = $comment_id;
        $this->user_id = $user_id;
        $this->text = $text;
        $this->his = $his;
    }
}