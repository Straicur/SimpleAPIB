<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Tools\DBTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

class RegistrationController extends MyController
{
    private  $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Endpoint który umożliwia użytkownikowi zarejestrowanie się oraz po poprawnej rejestracji wysyła email potwierdzający
     *
     * @Route("/user/register", name="app_register", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\GetUserQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct",
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
     * @return Response
     */
    public function register(Request $request): Response
    {
        $object = $this->getJsonData($request,'App\\Query\\GetUserQuery');

        list($allProvided, $missingAttributes) = $this->checkRrequiredDataFromQuery($object);

        if ($allProvided) {

            $entityManager = $this->getDoctrine();

            $dbTool = new DBTool($entityManager);

            $user = new User();
            $user->setEmail($object->email);
            $user->setPassword($object->password);
            $user->setRoles($object->role);


            $trans = $entityManager->getManager()->getConnection();
            $trans->beginTransaction();

            if (empty($dbTool->findBy(User::class, ["email" => $user->getEmail()]))) {
                try {

                    $dbTool->insertData($user);

                    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('admin@gmail.com', 'Service Bot'))
                            ->to($user->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );
                    $trans->commit();

                    return $this->getResponse();
                } catch (\Exception $e) {
                    $trans->rollBack();
                    print_r($e);
                    return $this->getResponse(null, 500);
                }
            } else {
                $trans->rollBack();
                return $this->getResponse(null, 500);
            }
        }
        else
        {
            return $this->getResponse(null, 400);
        }
    }

    /**
     * Endpoint który weryfikuje użytkownika i po tej weryfikacji tworzy podstawowe tabele w bazie dla niego
     *
     * @Route("/verify/email", name="app_verify_email", methods={"GET"})
     *
     * @SWG\Parameter(
     *     name="token_json",
     *     in="body",
     *     @Model(type=App\Query\VerifyEmailQuery::class)
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token was correct",
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
     * @return Response
     */
    public function verifyUserEmail(Request $request): Response
    {
        $entityManager = $this->getDoctrine();
        $dbTool = new DBTool($entityManager);

        $user =$dbTool->findBy(User::class, ["user_id" => $request->query->get('userID')]);

        if($user){
            try {
                $this->emailVerifier->handleEmailConfirmation($request,$user[0]);

                $trans = $entityManager->getManager()->getConnection();

                $trans->beginTransaction();

                $user[0]->setIsVerified(true);

                $dbTool->insertData($user[0]);

                $trans->commit();

                return $this->getResponse();
            }
            catch (VerifyEmailExceptionInterface $exception) {
                return $this->getResponse(null,500);
            }
        }
        else{
            return $this->getResponse(null,401);
        }

    }
}
