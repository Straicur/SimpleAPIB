<?php

namespace App\Query;

use App\Annotations\DataRequired;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class VerifyEmailQuery
 * @package App\Query
 */
class VerifyEmailQuery{
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @SWG\Property(type="string", maxLength=255, description="Email")
     */
    public $userId;
}