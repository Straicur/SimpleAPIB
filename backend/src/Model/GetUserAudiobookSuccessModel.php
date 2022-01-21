<?php

namespace App\Model;

use App\JsonModels\GetSetsModel;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class GetUserAudiobookSuccessModel
 * @package App\Model
 */
class GetUserAudiobookSuccessModel{
    /**
     * @var array
     *
     * @JMSA\Type("array<App\JsonModels\GetAudiobooksModel>")
     *
     * @SWG\Property(property="set-info",type="array", @SWG\Items(@Model(type=App\JsonModels\GetAudiobooksModel::class) ), description="Array of available audiook");
     *
     */
    public $get_audiobook_data;

    /**
     * @param array $audiobook
     */
    public function __construct(array $audiobook = []){
        $this->get_audiobook_data = $audiobook;
    }
}