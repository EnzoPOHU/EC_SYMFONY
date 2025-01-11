<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'auth.login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator, 
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');
        $password = $request->request->get('_password', '');

        $this->logger->info('Tentative d\'authentification', [
            'email' => $email,
            'password_length' => strlen($password)
        ]);

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        try {
            return new Passport(
                new UserBadge($email, function($email) {
                    $userRepository = $this->entityManager->getRepository('App\Entity\User');
                    $user = $userRepository->findOneBy(['email' => $email]);
                    if (!$user) {
                        $this->logger->warning('Utilisateur non trouvé', ['email' => $email]);
                        throw new BadCredentialsException('Utilisateur non trouvé');
                    }
                    return $user;
                }),
                new PasswordCredentials($password),
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'authentification', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = $token->getUser();

            // Logs détaillés pour le débogage
            $this->logger->info('Authentification réussie', [
                'user_email' => $user->getEmail(),
                'user_roles' => $user->getRoles(),
                'target_path' => $this->getTargetPath($request->getSession(), $firewallName),
                'firewall_name' => $firewallName
            ]);

            // Générer l'URL de la page d'accueil
            $homeUrl = $this->urlGenerator->generate('app.home');
            $this->logger->info('Redirection vers la page d\'accueil', ['url' => $homeUrl]);
            
            return new RedirectResponse($homeUrl);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la redirection après authentification', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Rediriger vers la page de login en cas d'erreur
            return new RedirectResponse($this->getLoginUrl($request));
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->logger->warning('Échec de l\'authentification', [
            'message' => $exception->getMessage(),
            'username' => $request->request->get('_username')
        ]);

        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->getLoginUrl($request));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
