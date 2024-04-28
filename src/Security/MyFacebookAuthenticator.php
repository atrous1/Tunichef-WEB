<?php

namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class MyFacebookAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
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
    $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
        'nom' => $googleUser->getFirstName(),
        'prenom' => $googleUser->getLastName(),
    ]);

    if (!$user) {
        $user = new Utilisateur();
        // Set user properties from GoogleUser
        $user->setNom($googleUser->getFirstName());
        $user->setPrenom($googleUser->getLastName());
        $user->setEmail($googleUser->getEmail());

        // Set default values for other fields
        $user->setRoles(['touriste']); // Default role
        $user->setPassword(''); // Empty password
        $user->setNumDeTelephone(''); // Empty phone number
        $user->setDatedenaissance(new \DateTime()); // Default date of birth

        // Persist user in the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    } else {
        // If the user already exists, you can update any other attributes here if needed
        // For example:
        if (!$user->getRoles()) {
            $user->setRoles(['touriste']); // Set default role if it's null
        }
        if (!$user->getPassword()) {
            $user->setPassword(''); // Set empty password if it's null
        }
        // Similarly, you can initialize other attributes here
    }

    return new SelfValidatingPassport(new UserBadge($user->getNom()));
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('app_utilisateur_home');

        return new RedirectResponse($targetUrl);
    
        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    
   /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}