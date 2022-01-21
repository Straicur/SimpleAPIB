<?php

namespace App\Query;

use App\Annotations\DataRequired;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class GetUserQuery
 * @package App\Query
 */
class GetUserQuery{
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="ZipDir")
     */
    public $email;
    /**
     * @var array
     *
     * @JMSA\Type("array")
     *
     * @DataRequired
     *
     * @SWG\Property(type="array", description="ZipDir",@SWG\Items(type="string"))
     */
    public $role;
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="Password")
     */
    public $password;
}
