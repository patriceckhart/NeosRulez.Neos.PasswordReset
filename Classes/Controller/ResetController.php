<?php
namespace NeosRulez\Neos\PasswordReset\Controller;

/*
 * This file is part of the NeosRulez.Neos.PasswordReset package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Error\Messages\Message;

class ResetController extends ActionController {

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
    public function executeAction(string $string, string $authenticationProviderName = 'Neos.Neos:Backend')
    {
        $result = false;
        try {
            if(@$this->userService->getUser($string, $authenticationProviderName)->getElectronicAddresses()) {
                foreach ($this->userService->getUser($string, $authenticationProviderName)->getElectronicAddresses() as $electronicAddress) {
                    if($electronicAddress->getType() == 'Email') {
                        $result = true;
                        $password = $this->userService->generatePassword(24);
                        $this->userService->setUserPassword($this->userService->getUser($string, $authenticationProviderName), $password);
                        $this->mailService->sendMail(['username' => $electronicAddress->getIdentifier(), 'password' => $password], $this->settings['senderMail'], $electronicAddress->getIdentifier(), 'Password Reset', $this->settings['templatePathAndFilename']);
                        if(array_key_exists('adminMail', $this->settings)) {
                            $this->mailService->sendMail(['username' => $electronicAddress->getIdentifier(), 'isInfo' => true], $this->settings['senderMail'], $this->settings['adminMail'], 'Password Reset', $this->settings['templatePathAndFilename']);
                        }
                    }
                }
            }
        } catch (\Error $e) {
            $result = false;
        }
        if($result) {
            $this->addFlashMessage($this->translator->translateById('content.credentialsSent', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_NOTICE);
        } else {
            $this->addFlashMessage($this->translator->translateById('content.userNotFound', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.PasswordReset'), '', Message::SEVERITY_ERROR);
        }
        $this->redirect('index', 'login', 'Neos.Neos');
    }

}
