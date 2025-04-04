<?php
namespace App\Security;

use App\Entity\Participant;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    public const LOGIN_ROUTE = 'app_login';

    private $urlGenerator;
    private $entityManager;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

//Gère l'authentification avec l'email ou le pseudo
    public function authenticate(Request $request): Passport
    {
        $identifier = $request->request->get('email');

        if (!$identifier) {
            throw new \Exception('Un identifiant (email ou pseudo) est obligatoire');
        }
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $identifier);

        $user = $this->getUserByEmailOrPseudo($identifier);

        return new Passport(
            new UserBadge($user->getUserIdentifier()), // Assuming getUserIdentifier returns either email or pseudo
            new PasswordCredentials($request->request->get('password')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    private function getUserByEmailOrPseudo(string $identifier): Participant
    {
        // Check l'email avec FILTER_VALIDATE_EMAIL
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {

            $user = $this->entityManager->getRepository(Participant::class)->findOneBy(['email' => $identifier]);
        } else {

            $user = $this->entityManager->getRepository(Participant::class)->findOneBy(['pseudo' => $identifier]);
        }

        if (!$user) {
            throw new CustomUserMessageAccountStatusException('Identifiant ou mot de passe incorrect');
        }

        return $user;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirection de la page si identification ok
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_main'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

}