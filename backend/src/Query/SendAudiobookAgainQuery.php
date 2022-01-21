<?php

namespace App\Query;

use App\Annotations\DataRequired;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class SendAudiobookAgainQuery
 * @package App\Query
 */
class SendAudiobookAgainQuery{
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="AuthToken")
     */
    public $token;
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="NameOfAudiobook")
     */
    public $name;
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="SetNameOrKey")
     */
    public $set_key;

    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="HashName")
     */
    public $hash_name;

    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", maxLength=255, description="File_name")
     */
    public $file_name;
    /**
     * @var integer
     *
     * @JMSA\Type("int")
     *
     * @DataRequired
     *
     * @SWG\Property(type="integer", description="total_size")
     */
    public $part_nr;
    /**
     * @var integer
     *
     * @JMSA\Type("int")
     *
     * @DataRequired
     *
     * @SWG\Property(type="integer", description="all_parts_nr")
     */
    public $all_parts_nr;
    /**
     * @var string
     *
     * @JMSA\Type("string")
     *
     * @DataRequired
     *
     * @SWG\Property(type="string", description="base64 of part")
     */
    public $base64;
}
