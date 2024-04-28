<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
{
    $googleClient = $this->clientRegistry->getClient('google');
    
    // Obtenez le jeton d'accès
    $accessToken = $googleClient->getAccessToken();
    
    // Vérifiez si le jeton d'accès est valide
    if ($accessToken->hasExpired()) {
        // Gérer l'expiration du jeton
    }
    
    /** @var GoogleUser $googleUser */
    $googleUser = $googleClient->fetchUserFromToken($accessToken);
    
    // Recherchez ou créez l'utilisateur dans votre base de données en utilisant l'adresse e-mail
    $user = $this->entityManager->getRepository(User::class)->findOneBy([
        'email' => $googleUser->getEmail(),
    ]);
    
    if (!$user) {
        // Si l'utilisateur n'existe pas, créez-le
        $user = new User();
        // Définissez les propriétés de l'utilisateur à partir de GoogleUser
        $user->setNom($googleUser->getFirstName());
        $user->setPrenom($googleUser->getLastName());
        $user->setEmail($googleUser->getEmail());
        $user->setImage($googleUser->getAvatar());
        $user->setRole('CLIENT'); 
        $user->setPassword(''); 
        $user->setNumtel(null); 
        $user->setStatut('ACTIVE');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
    
    return new SelfValidatingPassport(new UserBadge($user->getEmail()));
}



public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
{
    $warningMessage = 'Vous avez des infomations manquantes.';
    
    // Rediriger après une authentification réussie
    return new RedirectResponse($this->urlGenerator->generate('app_home', ['warningMessage' => $warningMessage]));
}



    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        // Handle authentication failure
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}