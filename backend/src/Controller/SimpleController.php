<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Token;
use App\Model\AdminAuthSuccessModel;
use App\Model\GetCommentModel;
use App\Model\LoginSuccessModel;
use App\Model\PostsSuccessModel;
use App\Model\PostSuccessModel;
use App\Tools\DBTool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerBuilder;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class SimpleController
 * @package App\Controller
 */
class SimpleController extends MyController {
    /**
     *
     * @Route("/user/login", name="user_login", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\LoginUserQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct",
     *     @Model(type=App\Model\LoginSuccessModel::class)
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginUser(Request $request): Response{

        $object = $this->getJsonData($request,'App\Query\LoginUserQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {
            $entityManager = $this->getDoctrine();
            $dbTool = new DBTool($entityManager);

            $email=$object->email;
            $password=$object->password;

            try {
                $user = $dbTool->findBy(User::class, ["email"=>$email,"password"=>$password,"isVerified" => 1]);

                if ($user[0]) {
                    $em = $dbTool->getEntityManager();

                    $transaction = $em->getConnection();
                    $transaction->beginTransaction();
                    try {
                        if($allTokens = $dbTool->findBy(Token::class, ["active" => 1, "user_id" => $user[0]->getId()]))
                        {
                            foreach ($allTokens as $oldToken){
                                $oldToken->setActive(false);

                                $dbTool->insertData($oldToken);
                            }
                        }


                        $newGeneratedToken = openssl_random_pseudo_bytes(64);
                        $newGeneratedToken = bin2hex($newGeneratedToken);

                        $newToken = new Token($user[0], $newGeneratedToken);

                        $dbTool->insertData($newToken);

                        $transaction->commit();

                        $generatedTocken = new LoginSuccessModel();

                        $generatedTocken->token = $newToken->getToken();

                        $generatedTocken = $this->makeJsonData($generatedTocken);

                        return $this->getResponse($generatedTocken,200);

                        //--------------------------------------------------------------------------------------------------------------

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        print_r($e);
                        return $this->getResponse(null, 501);
                    }
                } else {
                    return $this->getResponse(null, 401);
                }
            }
            catch (\Exception $e) {
                print_r($e);
                return $this->getResponse(null, 401);
            }
        }
        else {
            return $this->getResponse(null, 404);
        }
    }
    /**
     * Endpoint który umożliwiwia użytkownikowi wylogowanie się
     *
     * @Route("/user/posts", name="user_posts", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\GetPostsQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userPosts(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\GetPostsQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token)){
                if($user = $this->getUserByToken($object->token)){
                    $doctrine = $this->getDoctrine();
                    $dbTool = new DBTool($doctrine);
                    $preparedData=[];
                    $posts = $dbTool->findBy(Post::class, []);
                    foreach ($posts as $post){
                        $comments = $dbTool->findBy(Comment::class, ["post_id"=>$post->getPost_id()]);
                        if($comments){
                            $preparedData[] =  new PostsSuccessModel($post->getPost_id(),$post->getTitle(),$post->getText(),$post->getPost_date()->format('Y-m-d H:i:s'),count($comments));
                        }
                        else{
                            $preparedData[] =  new PostsSuccessModel($post->getPost_id(),$post->getTitle(),$post->getText(),$post->getPost_date()->format('Y-m-d H:i:s'),0);
                        }
                    }
                    return $this->getResponse($this->makeJsonData($preparedData));
                }
                else{
                    return $this->getResponse(null, 401);
                }
            }
            else{
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
    /**
     *
     * @Route("/user/comment/all", name="user_comment_all", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\GetPostQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userComments(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\GetPostQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token)){
                if($user = $this->getUserByToken($object->token)){

                    $userID=$user[0]->getId();

                    if($userID){
                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);

                        $post = $dbTool->findBy(Post::class, ["post_id"=>$object->post_id]);
                        if($post[0]){
                            $comments = $dbTool->findBy(Comment::class, ["post_id"=>$post[0]->getPost_id()]);
                            if($comments){
                                $comentsId = [];

                                foreach ($comments as $comment){
                                    $his = false;
                                    if($userID===$comment->getUser_id()->getId()){
                                        $his=true;
                                    }
                                    $comentsId[]= new GetCommentModel($comment->getComment_id(),$comment->getUser_id()->getId(),$comment->getText(),$his);
                                }
                                $preparedData =  new PostSuccessModel($post[0]->getPost_id(),$post[0]->getTitle(),$post[0]->getText(),$post[0]->getPost_date()->format('Y-m-d H:i:s'),$comentsId);
                            }
                            else{
                                $preparedData =  new PostSuccessModel($post[0]->getPost_id(),$post[0]->getTitle(),$post[0]->getText(),$post[0]->getPost_date()->format('Y-m-d H:i:s'),[]);
                            }

                            return $this->getResponse($this->makeJsonData($preparedData));
                        }
                        else{
                            return $this->getResponse(null, 500);
                        }
                    }
                    else{
                        return $this->getResponse(null, 500);
                    }
                }
                else{
                    return $this->getResponse(null, 401);
                }
            }
            else{
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
    /**
     *
     * @Route("/user/comment/add", name="user_comment_add", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\AddCommentQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userCommentAdd(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\AddCommentQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token)){
                if($user = $this->getUserByToken($object->token)){
                    if($user[0]){
                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);
                        $em = $dbTool->getEntityManager();
                        $transaction = $em->getConnection();
                        $transaction->beginTransaction();

                        $post = $dbTool->findBy(Post::class, ["post_id"=>$object->post_id]);
                        if($post[0])
                        {
                            $comment = new Comment($post[0],$user[0],$object->text);
                            $dbTool->insertData($comment);
                            $transaction->commit();
                            return $this->getResponse();
                        }
                        else{
                            return $this->getResponse(null, 500);
                        }
                    }
                    else{
                        return $this->getResponse(null, 500);
                    }
                }
                else{
                    return $this->getResponse(null, 401);
                }
            }
            else{
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
    /**
     *
     * @Route("/user/comment/delete", name="user_comment_delete", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\DeleteCommentQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userCommentDelete(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\DeleteCommentQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token)){

                if($user = $this->getUserByToken($object->token)){
                    $userID=$user[0]->getId();
                    if($userID){
                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);
                        $em = $dbTool->getEntityManager();
                        $transaction = $em->getConnection();
                        $transaction->beginTransaction();

                        $comment = $dbTool->findBy(Comment::class, ["comment_id"=>$object->comment_id]);
                        if($comment[0])
                        {

                            $dbTool->removeData($comment[0]);
                            $transaction->commit();

                            return $this->getResponse();
                        }
                        else{
                            return $this->getResponse(null, 500);
                        }
                    }
                    else{
                        return $this->getResponse(null, 500);
                    }
                }
                else{
                    return $this->getResponse(null, 401);
                }
            }
            else{
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
    /**
     *
     * @Route("/user/comment/edit", name="user_comment_edit", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\EditCommentQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userCommentEdit(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\EditCommentQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token)){
                if($user = $this->getUserByToken($object->token)){
                    $userID=$user[0]->getId();
                    if($userID){

                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);
                        $em = $dbTool->getEntityManager();
                        $transaction = $em->getConnection();

                        $transaction->beginTransaction();
                        $comment = $dbTool->findBy(Comment::class, ["comment_id"=>$object->comment_id,"user_id"=>$userID]);
                        if($comment[0])
                        {
                            $comment[0]->setText($object->text);

                            $dbTool->insertData($comment[0]);
                            $transaction->commit();

                            return $this->getResponse();
                        }
                        else{
                            return $this->getResponse(null, 500);
                        }
                    }
                    else{
                        return $this->getResponse(null, 500);
                    }
                }
                else{
                    return $this->getResponse(null, 401);
                }
            }
            else{
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
    /**
     * Endpoint który umożliwiwia użytkownikowi wylogowanie się
     *
     * @Route("/user/logout", name="user_logout", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\GetSetsQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The server could not understand the request due to invalid syntax"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized action user"
     * )
     *
     * @SWG\Response(
     *     response=501,
     *     description="Service configuration is incorrect"
     * )
     *
     * @SWG\Tag(name="User EP")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function logoutUser(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\GetPostsQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {
            if ($this->authorizeToken($object->token)){
                if($this->logout($object->token)){
                    return $this->getResponse();
                }
                else{
                    return $this->getResponse(null, 500);
                }
            }
            else{
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
}
