<?php

namespace App\Model;

use App\JsonModels\GetSetsModel;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class GetSetsDataSuccessModel
 * @package App\Model
 */
class GetSetsDataSuccessModel{
    /**
     * @var array
     *
     * @JMSA\Type("array<App\JsonModels\GetSetsAudiobooksModel>")
     *
     * @SWG\Property(property="audiobooks-info",type="array", @SWG\Items(@Model(type=App\JsonModels\GetSetsAudiobooksModel::class) ), description="Array of available sets");
     *
     */
    public $get_audiobooks_data;

    /**
     * @param array $audiobooks
     */
    public function __construct(array $audiobooks = []){
        $this->get_audiobooks_data = $audiobooks;
    }
}
