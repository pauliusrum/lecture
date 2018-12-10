<?php declare(strict_types=1);

namespace App\Session;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class SessionManager
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser(): ?User
    {
        $anonymous = 'anon.';
        $token = $this->security->getToken();
        $user = $token ? $token->getUser() : $anonymous;

        return $user === $anonymous ? null : $user;
    }
}