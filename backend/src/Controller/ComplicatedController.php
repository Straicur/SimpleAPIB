<?php

namespace App\Controller;

use App\Entity\AdminToken;
use App\Entity\AdminUser;
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
 * Class ComplicatedController
 * @package App\Controller
 */
class ComplicatedController extends MyController {
    /**
     *
     * @Route("/admin/login", name="admin_login", methods={"POST"})
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
    public function loginAdmin(Request $request): Response{

        $object = $this->getJsonData($request,'App\Query\LoginUserQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {
            $entityManager = $this->getDoctrine();
            $dbTool = new DBTool($entityManager);

            $email=$object->email;
            $password=$object->password;

            try {

                $user = $dbTool->findBy(AdminUser::class, ["email"=>$email,"password"=>$password]);

                if ($user[0]) {
                    $em = $dbTool->getEntityManager();

                    $transaction = $em->getConnection();
                    $transaction->beginTransaction();
                    try {
                        if($allTokens = $dbTool->findBy(AdminToken::class, ["active" => 1, "admin_id" => $user[0]->getId()]))
                        {

                            foreach ($allTokens as $oldToken){
                                $oldToken->setActive(false);

                                $dbTool->insertData($oldToken);
                            }
                        }

                        $newGeneratedToken = openssl_random_pseudo_bytes(64);
                        $newGeneratedToken = bin2hex($newGeneratedToken);

                        $newToken = new AdminToken($user[0], $newGeneratedToken);

                        $dbTool->insertData($newToken);

                        $transaction->commit();

                        $generatedTocken = new LoginSuccessModel();

                        $generatedTocken->token = $newToken->getToken();

                        $generatedTocken = $this->makeJsonData($generatedTocken);

                        return $this->getResponse($generatedTocken,203);

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
     * @Route("/admin/posts", name="admin_posts", methods={"POST"})
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
    public function adminPosts(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\GetPostsQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token,true)){
                if($user = $this->getUserByToken($object->token, true)){
                    $doctrine = $this->getDoctrine();
                    $dbTool = new DBTool($doctrine);
                    $preparedData=[];
                    $posts = $dbTool->findBy(Post::class, ["admin_id" => $user[0]->getId()]);
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
     * @Route("/admin/post/add", name="admin_posts_add", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\AddPostQuery::class)
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
    public function adminPostsAdd(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\AddPostQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token,true)){
                if($user = $this->getUserByToken($object->token, true)){
                    if($user[0]){
                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);
                        $em = $dbTool->getEntityManager();
                        $transaction = $em->getConnection();
                        $transaction->beginTransaction();

                        $post = new Post($user[0],$object->title,$object->text);

                        $dbTool->insertData($post);
                        $transaction->commit();

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
                return $this->getResponse(null, 401);
            }
        }
        else{
            return $this->getResponse(null, 400);
        }
    }
    /**
     *
     * @Route("/admin/post/edit", name="admin_posts_edit", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\EditPostQuery::class)
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
    public function adminPostsEdit(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\EditPostQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token,true)){
                if($user = $this->getUserByToken($object->token, true)){
                    $userID=$user[0]->getId();
                    if($userID){

                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);
                        $em = $dbTool->getEntityManager();
                        $transaction = $em->getConnection();

                        $transaction->beginTransaction();
                        $post = $dbTool->findBy(Post::class, ["post_id"=>$object->post_id]);
                        if($post[0])
                        {
                            $post[0]->setPost_date();
                            $post[0]->setTitle($object->title);
                            $post[0]->setText($object->text);
                            $dbTool->insertData($post[0]);
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
     * @Route("/admin/post/delete", name="admin_posts_delete", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\DeletePostQuery::class)
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
    public function adminPostsDelete(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\DeletePostQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token,true)){

                if($user = $this->getUserByToken($object->token, true)){
                    $userID=$user[0]->getId();
                    if($userID){
                        $doctrine = $this->getDoctrine();
                        $dbTool = new DBTool($doctrine);
                        $em = $dbTool->getEntityManager();
                        $transaction = $em->getConnection();
                        $transaction->beginTransaction();

                        $post = $dbTool->findBy(Post::class, ["post_id"=>$object->post_id]);
                        if($post[0])
                        {
                            $comments = $dbTool->findBy(Comment::class, ["post_id"=>$post[0]->getPost_id()]);
                            if($comments){
                                foreach ($comments as $comment){
                                    $dbTool->removeData($comment,false);
                                }
                            }
                            $dbTool->removeData($post[0]);
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
     * @Route("/admin/post", name="admin_post", methods={"POST"})
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
    public function adminPost(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\GetPostQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            if ($this->authorizeToken($object->token,true)){
                if($user = $this->getUserByToken($object->token, true)){

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
                                    $comentsId[]= new GetCommentModel($comment->getComment_id(),$comment->getUser_id()->getId(),$comment->getText(),false);
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
     * Endpoint który umożliwiwia użytkownikowi wylogowanie się
     *
     * @Route("/admin/logout", name="admin_logout", methods={"POST"})
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
    public function logoutAdmin(Request $request): Response{
        $object = $this->getJsonData($request,'App\Query\GetPostsQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {
            if ($this->authorizeToken($object->token,true)){
                if($this->logout($object->token,true)){
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
