<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;


class LogInFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user && $user->getStatut() === 'BANNED') {
            throw new CustomUserMessageAuthenticationException('Votre compte a été banni. Vous ne pouvez pas vous connecter.');
        }    

        //$request->getSession()->set(Security::LAST_USERNAME, $email);
        // Vérifier le nombre de tentatives de connexion infructueuses
        $failedAttempts = $request->getSession()->get('failed_login_attempts', 0);
        $request->getSession()->set('failed_login_attempts', $failedAttempts + 1);
    
        // Vérifier si l'utilisateur est banni
        if ($failedAttempts >= 3) {
            // Bannir l'utilisateur
            $this->banUser($email);
            // Réinitialiser le compteur de tentatives de connexion infructueuses
            $request->getSession()->set('failed_login_attempts', 0);
        }            

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),            ]
        );
    }


private function banUser(string $email): void
{
    // Récupérer l'utilisateur à partir de l'email
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    // Mettre à jour le statut de l'utilisateur à "BANNED"
    $user->setStatut('BANNED');

    // Enregistrer les modifications dans la base de données
    $this->entityManager->flush();
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Get the user's role
        $role = $token->getUser()->getRole();

        // Check if the user has the role "ADMIN"
        if ($role === 'ADMIN') {
            return new RedirectResponse($this->urlGenerator->generate('app_user_back'));
        }

        // Check if the user has the role "CLIENT"
        if ($role === 'CLIENT') {
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }

        // If user has neither "ADMIN" nor "CLIENT" role, redirect to a default route
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
