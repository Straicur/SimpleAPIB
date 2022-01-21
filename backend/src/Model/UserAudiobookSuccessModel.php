<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class GetSetsAudiobooksJsonModel
 * @package App\JsonModel
 */
class UserAudiobookSuccessModel{

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$title");
     */
    public $title;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$album");
     */
    public $album;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$author");
     */
    public $author;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$year");
     */
    public $year;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$desc");
     */
    public $desc;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$comments");
     */
    public $comments;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$duration");
     */
    public $duration;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="$size");
     */
    public $size;
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="parts");
     */
    public $parts;
    /**
     * @var boolean
     *
     * @SWG\Property(type="boolean", description="like");
     */
    public $like;

    function __construct(string $title,string $album,string $author,string $year,string $desc,string $comments,string $duration,string $size,string $parts,string $like)
    {
        $this->title = $title;
        $this->album = $album;
        $this->author = $author;
        $this->year = $year;
        $this->desc = $desc;
        $this->comments = $comments;
        $this->duration = $duration;
        $this->size = $size;
        $this->parts = $parts;
        $this->like = $like;
    }
}