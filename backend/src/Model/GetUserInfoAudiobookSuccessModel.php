<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class GetUserInfoAudiobookSuccessModel{
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="ended_time");
     */
    public $ended_time;
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="part_nr");
     */
    public $part_nr;
    function __construct(string $ended_time, string $part_nr)
    {
        $this->ended_time = $ended_time;
        $this->part_nr = $part_nr;
    }
}