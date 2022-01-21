<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class GetAudiobookModel{
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="Dir");
     */
    public $folder_dir;
}