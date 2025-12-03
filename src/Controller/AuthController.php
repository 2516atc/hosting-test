<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\SessionAttributeKey;
use App\Form\Type\AdminTokenType;
use App\Http\Github\Github;
use App\Http\Session;
use App\Repository\UserRepository;
use App\Security\GithubProviderFactory;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth')]
class AuthController extends AbstractController
{
    private readonly string $adminTokenFile;

    function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GithubProviderFactory $githubProviderFactory,
        private readonly Security $security,
        KernelInterface $kernel
    )
    {
        $this->adminTokenFile = $kernel->getProjectDir() . '/var/admin_token.txt';
    }

    #[Route('', name: 'auth', methods: ['GET'])]
    public function auth(): Response
    {
        return $this->render('auth.login.html.twig');
    }

    /**
     * @throws GuzzleException
     * @throws RandomException
     * @throws IdentityProviderException
     */
    #[Route('/github', name: 'auth_github', methods: ['GET', 'POST'])]
    public function githubAuth(Github $github, Request $request, UserRepository $userRepository): Response
    {
        if ($request->isMethod(Request::METHOD_POST))
            return $this->startAuthFlow($request);

        if (!$this->checkState($request))
            throw new BadRequestHttpException('Invalid state');

        Session::fromRequest($request)->remove(SessionAttributeKey::AUTH_STATE);

        $token = $this->getGithubAccessToken(
            $request->query->get('code')
        );

        $githubUser = $github->withToken($token)->user();
        $githubUserId = $githubUser['id'];
        $githubUserName = $githubUser['login'];

        $user = $userRepository->findOneByGithubUserId($githubUserId);

        if ($user === null && $userRepository->count() === 0)
            return $this->startAdminTokenFlow($request, $githubUserId, $githubUserName);

        if ($this->security->login($user) === null)
            return $this->redirectToRoute('auth');

        return $this->redirectToRoute('configuration_all');
    }

    #[Route('/token', name: 'auth_admin_token', methods: ['GET', 'POST'])]
    public function adminToken(Request $request): Response
    {
        $session = Session::fromRequest($request);
        $githubUserId = $session->get(SessionAttributeKey::GITHUB_USER_ID);

        if(!file_exists($this->adminTokenFile) || !$githubUserId)
            return $this->redirectToRoute('auth');

        $form = $this->createForm(AdminTokenType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $userAdminToken = $form->getData()['adminToken'];
            $storedAdminToken = file_get_contents($this->adminTokenFile);

            if ($userAdminToken !== $storedAdminToken)
            {
                $session->remove(SessionAttributeKey::GITHUB_USER_ID);
                $session->remove(SessionAttributeKey::GITHUB_LOGIN);

                return $this->redirectToRoute('auth');
            }

            $user = $this->createUserFromGithubUser(
                $session->get(SessionAttributeKey::GITHUB_USER_ID),
                $session->get(SessionAttributeKey::GITHUB_LOGIN)
            );

            $this->security->login($user);

            unlink($this->adminTokenFile);
            $session->remove(SessionAttributeKey::GITHUB_USER_ID);

            return $this->redirectToRoute('configuration_all');
        }

        return $this->render('auth.token.html.twig', [
            'form' => $form,
        ]);
    }

    private function checkState(Request $request): bool
    {
        $state = $request->query->get('state');

        if ($state === null)
            return false;

        return $state === Session::fromRequest($request)->get(SessionAttributeKey::AUTH_STATE);
    }

    private function createUserFromGithubUser(int $githubUserId, string $githubLogin): User
    {
        $user = new User();

        $user->setGithubUserId($githubUserId);
        $user->setUsername($githubLogin);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @throws GuzzleException
     * @throws IdentityProviderException
     */
    private function getGithubAccessToken(string $authorizationCode): string
    {
        $provider = $this->githubProviderFactory->getProvider();

        return $provider->getAccessToken('authorization_code', [
            'code' => $authorizationCode,
            'scope' => ['user', 'user:email']
        ]);
    }

    /**
     * @throws RandomException
     */
    private function startAdminTokenFlow(Request $request, int $githubUserId, string $githubLogin): Response
    {
        $session = Session::fromRequest($request);
        $session->set(SessionAttributeKey::GITHUB_USER_ID, $githubUserId);
        $session->set(SessionAttributeKey::GITHUB_LOGIN, $githubLogin);

        if (!file_exists($this->adminTokenFile))
            file_put_contents($this->adminTokenFile, bin2hex(random_bytes(16)));

        return $this->redirectToRoute('auth_admin_token');
    }

    /**
     * @throws RandomException
     */
    private function startAuthFlow(Request $request): Response
    {
        $provider = $this->githubProviderFactory->getProvider();
        $state = md5(random_bytes(10));

        Session::fromRequest($request)->set(SessionAttributeKey::AUTH_STATE, $state);

        return $this->redirect(
            $provider->getAuthorizationUrl([
                'state' => $state,
                'scope' => ['user', 'user:email']
            ])
        );
    }
}
