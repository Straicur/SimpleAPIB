<?php

namespace App\Model;

use App\JsonModels\GetSetsModel;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class GetSetsSuccessModel
 * @package App\Model
 */
class GetSetsSuccessModel{
    /**
     * @var array
     *
     * @JMSA\Type("array<App\JsonModels\GetSetsModel>")
     *
     * @SWG\Property(property="set-info",type="array", @SWG\Items(@Model(type=App\JsonModels\GetSetsModel::class) ), description="Array of available sets");
     *
     */
    public $get_set_data;

    /**
     * @param array $sets
     */
    public function __construct(array $sets = []){
        $this->get_set_data = $sets;
    }
}