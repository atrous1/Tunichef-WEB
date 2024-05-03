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

    // Get the access token
    $accessToken = $googleClient->getAccessToken();

    // Check if the access token is valid
    if ($accessToken->hasExpired()) {
        // Handle token expiration
    }

    /** @var GoogleUser $googleUser */
    $googleUser = $googleClient->fetchUserFromToken($accessToken);

    // Find or create the user in your database using name and last name
    $user = $this->entityManager->getRepository(User::class)->findOneBy([
        'nom' => $googleUser->getFirstName(),
        'prenom' => $googleUser->getLastName(),
    ]);

    if (!$user) {
        $user = new User();
        // Set user properties from GoogleUser
        $user->setNom($googleUser->getFirstName());
        $user->setPrenom($googleUser->getLastName());
        $user->setEmail($googleUser->getEmail());
        // Set default values for other fields
        $user->setRole('CLIENT'); 
        $user->setPassword(''); 
        $user->setNumtel(null); 
        $user->setStatut('ACTIVE');

        // Persist user in the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    return new SelfValidatingPassport(new UserBadge($user->getNom()));
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        // Redirect after successful authentication
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        // Handle authentication failure
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}