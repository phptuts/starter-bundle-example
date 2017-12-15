<?php

namespace AppBundle\Entity;

use StarterKit\StartBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
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
    /**
     * @var string
     *
     * @ORM\Column(name="auth_token", type="string", nullable=true)
     */
    protected $authToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="auth_token_expire", type="datetime", nullable=true)
     */
    protected $authTokenExpire;

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @param string $authToken
     * @return User
     */
    public function setAuthToken(string $authToken)
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAuthTokenExpire()
    {
        return $this->authTokenExpire;
    }

    /**
     * @param \DateTime $authTokenExpire
     * @return User
     */
    public function setAuthTokenExpire(\DateTime $authTokenExpire)
    {
        $this->authTokenExpire = $authTokenExpire;

        return $this;
    }
}