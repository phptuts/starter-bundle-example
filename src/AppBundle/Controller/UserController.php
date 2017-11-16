<?php

namespace AppBundle\Controller;

use StarterKit\StartBundle\Form\ForgetPasswordType;
use StarterKit\StartBundle\Form\RegisterType;
use StarterKit\StartBundle\Form\ResetPasswordType;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\AuthResponseServiceInterface;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var AuthResponseServiceInterface
     */
    private $authResponseService;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(UserServiceInterface $userService,
                                AuthResponseServiceInterface $credentialResponseService,
                                AuthenticationUtils $authenticationUtils)
    {
        $this->userService = $userService;
        $this->authResponseService = $credentialResponseService;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/login", name="login")
     *
     * @return Response
     */
    public function loginAction()
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param  Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userService->registerUser($form->getData());

            $model = $this->authResponseService->createResponseAuthModel($user);

            $response = $this->redirectToRoute('homepage');
            $response->headers->setCookie(
                new Cookie(
                    AuthResponseService::AUTH_COOKIE,
                    $model->getAuthToken(),
                    $model->getTokenExpirationTimeStamp(),
                    null,
                    false,
                    false
                )
            );

            return $response;
        }

        return $this->render('register/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $token
     *
     * @Route("/reset-password/{token}", name="reset_password")
     *
     * @return Response
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->userService->findUserByForgetPasswordToken(urldecode($token));

        if (empty($user)) {

            return $this->render('reset-password/reset-password-invalid-token.html.twig');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUserForResetPassword($user);

            return $this->redirectToRoute('reset_password_success');
        }

        return $this->render('reset-password/reset-password.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/reset-password-success", name="reset_password_success")
     * @return Response
     */
    public function resetPasswordSuccessAction()
    {
        return $this->render('reset-password/reset-password-success.html.twig');
    }

    /**
     * @param Request $request
     * @Route("/forget-password", name="forget_password")
     *
     * @return Response
     */
    public function forgetPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->forgetPassword($form->getData());

            return $this->redirectToRoute('forget_password_success');
        }

        return $this->render('forget-password/forget-password.html.twig', [
            'forgetPasswordForm' => $form->createView()

        ]);
    }

    /**
     * @Route("/forget-password-success", name="forget_password_success")
     *
     * @return Response
     */
    public function forgetPasswordSuccessAction()
    {
        return $this->render('forget-password/forget-password-success.html.twig', ['title' => 'Forget Password']);
    }

    /**
     * Deletes the auth cookie and return the user to the home page
     * @Route("/logout", name="logout")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction()
    {
        $response = $this->redirectToRoute('homepage');
        $response->headers->clearCookie(AuthResponseService::AUTH_COOKIE);

        return $response;
    }
}