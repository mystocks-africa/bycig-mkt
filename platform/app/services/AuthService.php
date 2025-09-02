<?php
namespace App\Services;

use App\Core\Auth\Session;
use App\Core\Auth\Cookie;
use App\Core\Mailers\VerificationCode;
use App\Core\Mailers\HTMLMessages;
use App\Core\Mailers\Mailer;

use App\Core\Templates\DbTemplate;
use App\Models\User\Repository as UserRepository;
use App\Models\User\Entity as UserEntity;
use Exception;

class AuthService 
{
    private DbTemplate $db;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->userRepository = new UserRepository($this->db->getMysqli());
    }

    public function getClusterLeaderEmails(): array
    {
        $clusterLeaders = $this->userRepository->findAllClusterLeaders();

        // O(n) is fine because cluster leaders will always be small
        $clusterLeaderEmails = [];

        if (!empty($clusterLeaders) && is_array($clusterLeaders)) {
            foreach ($clusterLeaders as $leader) {
                if (isset($leader['email'])) {
                    $clusterLeaderEmails[] = $leader['email'];
                }
            }
        }

        return $clusterLeaderEmails;
    }

    public function validateUserAccount(?string $email, ?string $pwd, Session $session): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (isset($user) && password_verify($pwd, $user["pwd"])) {
            $sessionId  = $session->setSession($user["email"], $user["role"]);
            Cookie::assignSessionCookie($sessionId);
        } else {
            throw new Exception("Problem with logging in. Try again.");
        } 
    }

    public function createUserAccount(?string $email, ?string $pwd, ?string $clusterLeader, ?string $fullName): void
    {

        $hashPwd = password_hash($pwd, PASSWORD_DEFAULT);

        $userEntity = new UserEntity(
            $email, 
            $hashPwd, 
            $clusterLeader, 
            $fullName
        );

        $this->userRepository->save($userEntity);
    }

    public function deleteSession(Session $session): void
    {
        $session->deleteSession();
        Cookie::clearSessionCookie();
    }

    public function sendForgotPwdCode(?string $email): void
    {
        $code = VerificationCode::generateCode($email);
        $message = HTMLMessages::getForgottenPassword($code);
        Mailer::send($email, $message);
    }

    public function validateForgotPwdCode(?string $email, ?string $code, ?string $newPwd): void 
    {
        if (VerificationCode::verifyCode($email, $code) === true) {
            $hashNewPwd = password_hash($newPwd, PASSWORD_DEFAULT);
            $this->userRepository->updatePwd($hashNewPwd, $email);
        } else {
            throw new Exception("Validation of code has failed!");
        }
    }
}