<?php

namespace App\Model;

use App\JsonModels\GetSetsModel;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSA;

/**
 * Class UserInfoSuccessModel
 * @package App\Model
 */
class UserInfoSuccessModel{
    /**
     * @var array
     *
     * @JMSA\Type("array<App\JsonModels\GetUserJsonModel>")
     *
     * @SWG\Property(property="user-info",type="array", @SWG\Items(ref=@SWG\Schema(ref=@API\Model(type=App\JsonModels\GetUserJsonModel::class))), description="Array of all users in institution");
     *
     */
    public $get_user_data;

    /**
     * @param array $get_user
     */
    public function __construct(array $get_user = []){
        $this->get_user_data = $get_user;
    }
}