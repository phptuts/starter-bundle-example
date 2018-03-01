<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use StarterKit\StartBundle\Form\ChangePasswordType;
use StarterKit\StartBundle\Form\UpdateUserType;
use StarterKit\StartBundle\Service\FileUploadInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountSettingController
 * @package AppBundle\Controller
 */
class AccountSettingController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var FileUploadInterface
     */
    private $fileUplaodService;

    public function __construct(UserServiceInterface $userService, FileUploadInterface $fileUploadService)
    {
        $this->userService = $userService;
        $this->fileUplaodService = $fileUploadService;
    }

    /**
     * @Route("/account-settings/information", name="update_user")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     *
     * @return Response
     */
    public function updateUserAction(Request $request)
    {
        $form = $this->createForm(UpdateUserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();
            if (!empty($user->getImage())) {
                $url = $this->fileUplaodService->uploadFileWithFolderAndName($user->getImage(), 'profile_pics', md5($user->getId() . '_profile_id'));
                $user->setImageUrl($url);
            }

            $this->userService->save($user);
            $this->addFlash('success', 'Your profile was successfully updated!');
        }

        return $this->render('account-settings/update-user.html.twig', [
            'updateUserForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @Route("/account-settings/change-password", name="change_password")
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();
            $user->setPlainPassword($form->get('newPassword')->getData());
            $this->userService->saveUserWithPlainPassword($user);
            $this->addFlash('success', 'Your password was updated!');
        }

        return $this->render('account-settings/change-password.html.twig', [
            'changePasswordForm' => $form->createView()
        ]);
    }
}