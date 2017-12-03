<?php

namespace AppBundle\Entity;

use StarterKit\StartBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="StarterKit\StartBundle\Repository\UserRepository")
 * @ORM\Table(name="User")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="User", indexes={
 *     @ORM\Index(name="idk_email", columns={"email"}),
 *     @ORM\Index(name="idk_google_user_id", columns={"google_user_id"}),
 *     @ORM\Index(name="idk_slack_user_Id", columns={"slack_user_id"}),
 *     @ORM\Index(name="idk_facebook_user_id", columns={"facebook_user_id"}),
 *     @ORM\Index(name="idk_forget_password_token", columns={"forget_password_token"}),
 *     @ORM\Index(name="idk_refresh_token", columns={"refresh_token"})
 * })
 * Class User
 * @package AppBundle\Entity
 */
class User extends BaseUser
{

}