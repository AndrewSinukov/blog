<?php

namespace App\Security;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    private const EDIT = 'edit';

    private const DELETE = 'delete';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * MicroPostVoter constructor.
     *
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (! \in_array($attribute, [self::EDIT, self::DELETE], true)) {
            return false;
        }

        if (! $subject instanceof MicroPost) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(
        $attribute,
        $subject,
        TokenInterface $token
    ): bool {
        if ($this->decisionManager->decide($token, [User::ROLE_ADMIN])) {
            return true;
        }

        $authenticatedUser = $token->getUser();

        if (! $authenticatedUser instanceof User) {
            return false;
        }

        /** @var MicroPost $microPost */
        $microPost = $subject;

        return $microPost->getUser()->getId() === $authenticatedUser->getId();
    }
}