<?php

namespace App\Query;

use App\Annotations\DataRequired;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class AddPostQuery
 * @package App\Query
 */
class AddPostQuery{
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="token")
     */
    public $token;
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="title")
     */
    public $title;
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="text")
     */
    public $text;


}