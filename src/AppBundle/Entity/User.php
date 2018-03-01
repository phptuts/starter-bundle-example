<?php

namespace AppBundle\Entity;

use StarterKit\StartBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;
use StarterKit\StartBundle\Entity\FacebookTrait;
use StarterKit\StartBundle\Entity\GoogleTrait;
use StarterKit\StartBundle\Entity\ImageTrait;
use StarterKit\StartBundle\Entity\RefreshTokenTrait;
use StarterKit\StartBundle\Entity\SlackTrait;

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
 *     @ORM\Index(name="idk_refresh_token", columns={"refresh_token"}),
 *     @ORM\Index(name="idk_linked_in_user_id", columns={"linked_in_user_id"})
 * })
 * Class User
 * @package AppBundle\Entity
 */
class User extends BaseUser
{
    use ImageTrait;

    use FacebookTrait;

    use GoogleTrait;

    use SlackTrait;

    use RefreshTokenTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="linked_in_user_id", type="string", nullable=true)
     */
    protected $linkedInUserId;

    /**
     * @return string
     */
    public function getLinkedInUserId()
    {
        return $this->linkedInUserId;
    }

    /**
     * @param string $linkedInUserId
     * @return User
     */
    public function setLinkedInUserId($linkedInUserId)
    {
        $this->linkedInUserId = $linkedInUserId;

        return $this;
    }
}