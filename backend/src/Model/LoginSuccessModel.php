<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class LoginSuccessModel{
    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255, description="Token");
     */
    public $token;
}