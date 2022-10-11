<?php
namespace NeosRulez\Neos\PasswordReset\Controller;

/*
 * This file is part of the NeosRulez.Neos.PasswordReset package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Error\Messages\Message;

class ResetController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\PasswordReset\Domain\Service\UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\PasswordReset\Domain\Service\MailService
     */
    protected $mailService;

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\PasswordReset\Domain\Repository\TokenRepository
     */
    protected $tokenRepository;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\I18n\Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * @param string $string
     * @param string $authenticationProviderName
     * @return void
     */
    public function executeAction(string $string, string $authenticationProviderName = 'Neos.Neos:Backend'):void
    {
        if($this->userService->getUser($string, $authenticationProviderName)) {
            $user = $this->userService->getUser($string, $authenticationProviderName);

            $userMail = false;
            $accountIdentifier = $user->getAccounts()[0]->getAccountIdentifier();
            if(filter_var($accountIdentifier, FILTER_VALIDATE_EMAIL)) {
                $userMail = $accountIdentifier;
            }

            $protocol = !str_contains(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') ? 'http' : 'https';
            $host = $protocol . '://' . $_SERVER['HTTP_HOST'];
            $token = $this->tokenRepository->persist($user);
            $subject = $this->translator->translateById('content.resetPassword', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset');

            if(!$userMail) {
                if($user->getElectronicAddresses()) {
                    foreach ($user->getElectronicAddresses() as $electronicAddress) {
                        if($electronicAddress->getType() == 'Email') {
                            $this->mailService->sendMail(['mailVariables' => (array_key_exists('mailVariables', $this->settings) ? $this->settings['mailVariables'] : false), 'email' => $electronicAddress->getIdentifier(), 'host' => $host, 'domain' => $_SERVER['HTTP_HOST'], 'token' => $token], $this->settings['senderMail'], $electronicAddress->getIdentifier(), $subject, $this->settings['confirmation']['templatePathAndFilename']);
                        }
                    }
                }
            } else {
                $this->mailService->sendMail(['mailVariables' => (array_key_exists('mailVariables', $this->settings) ? $this->settings['mailVariables'] : false), 'email' => $userMail, 'host' => $host, 'domain' => $_SERVER['HTTP_HOST'], 'token' => $token], $this->settings['senderMail'], $userMail, $subject, $this->settings['confirmation']['templatePathAndFilename']);
            }

            $this->addFlashMessage($this->translator->translateById('content.confirmationSent', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_NOTICE);

        } else {
            $this->addFlashMessage($this->translator->translateById('content.userNotFound', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_ERROR);
        }

        $this->redirect('index', 'login', 'Neos.Neos');
    }

    /**
     * @param string $token
     * @param string $authenticationProviderName
     * @return void
     */
    public function setNewPasswordAction(string $token, string $authenticationProviderName = 'Neos.Neos:Backend'):void
    {
        $tokens = $this->tokenRepository->findByToken($token);
        if($tokens->count() > 0) {
            if($tokens->getFirst()->isValid()) {
                $protocol = !str_contains(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') ? 'http' : 'https';
                $host = $protocol . '://' . $_SERVER['HTTP_HOST'];
                $password = $this->userService->generatePassword($this->settings['passwordLength']);
                $this->userService->setUserPassword($tokens->getFirst()->getUser(), $password);

                $userMail = false;
                $accountIdentifier = $tokens->getFirst()->getUser()->getAccounts()[0]->getAccountIdentifier();
                if(filter_var($accountIdentifier, FILTER_VALIDATE_EMAIL)) {
                    $userMail = $accountIdentifier;
                }
                $subject = $this->translator->translateById('content.credentialsSent', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset');

                if(!$userMail) {
                    if(@$tokens->getFirst()->getUser()->getElectronicAddresses()) {
                        foreach ($tokens->getFirst()->getUser()->getElectronicAddresses() as $electronicAddress) {
                            if ($electronicAddress->getType() == 'Email') {
                                $this->mailService->sendMail(['mailVariables' => (array_key_exists('mailVariables', $this->settings) ? $this->settings['mailVariables'] : false), 'host' => $host, 'domain' => $_SERVER['HTTP_HOST'], 'email' => $electronicAddress->getIdentifier(), 'user' => $tokens->getFirst()->getUser(), 'username' => $tokens->getFirst()->getUser()->getAccounts()[0]->getAccountIdentifier(), 'password' => $password], $this->settings['senderMail'], $electronicAddress->getIdentifier(), $subject, $this->settings['reset']['templatePathAndFilename']);
                            }
                        }
                    }
                } else {
                    $this->mailService->sendMail(['mailVariables' => (array_key_exists('mailVariables', $this->settings) ? $this->settings['mailVariables'] : false), 'host' => $host, 'domain' => $_SERVER['HTTP_HOST'], 'email' => $userMail, 'user' => $tokens->getFirst()->getUser(), 'username' => $tokens->getFirst()->getUser()->getAccounts()[0]->getAccountIdentifier(), 'password' => $password], $this->settings['senderMail'], $userMail, $subject, $this->settings['reset']['templatePathAndFilename']);
                }

                if(array_key_exists('adminMail', $this->settings)) {
                    $subject = $this->translator->translateById('content.credentialsSent', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset');
                    $this->mailService->sendMail(['mailVariables' => (array_key_exists('mailVariables', $this->settings) ? $this->settings['mailVariables'] : false), 'host' => $host, 'domain' => $_SERVER['HTTP_HOST'], 'username' => $tokens->getFirst()->getUser()->getAccounts()[0]->getAccountIdentifier(), 'isInfo' => true], $this->settings['senderMail'], $this->settings['adminMail'], $subject, $this->settings['reset']['templatePathAndFilename']);
                }
                $this->tokenRepository->setInvalid($tokens->getFirst());
                $this->addFlashMessage($this->translator->translateById('content.passwordReset', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_NOTICE);
            } else {
                $this->addFlashMessage($this->translator->translateById('content.tokenInvalid', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_ERROR);
            }
        } else {
            $this->addFlashMessage($this->translator->translateById('content.tokenInvalid', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_ERROR);
        }
        $this->redirect('index', 'login', 'Neos.Neos');
    }

}
