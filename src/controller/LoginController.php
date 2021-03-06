<?php

namespace controller;


class LoginController
{

    private $loginModel;
    private $loginView;
    /**
     * @var RegisterController
     */
    private $registerController;

    /**
     * LoginController constructor.
     *
     * @param \model\LoginModel $loginModel
     * @param \view\LoginView $loginView
     * @param RegisterController $registerController
     */
    public function __construct(\model\LoginModel $loginModel,
                                \view\LoginView $loginView,
                                \controller\RegisterController $registerController)
    {
        $this->loginModel = $loginModel;
        $this->loginView = $loginView;
        $this->registerController = $registerController;
    }

    /**
     * @return mixed
     */
    public function getIsLoggedIn()
    {
        $currentUser = $this->loginView->getUserClient();

        return $this->loginModel->isLoggedIn($currentUser);
    }

    /**
     * Main login control, checks what the user wants to do and does it.
     */
    public function doControl()
    {
        if ($this->loginView->userWantsToRegister()) {

            $newlyRegistered = $this->registerController->doControl();
            if ($newlyRegistered != false) {
                $this->loginView->setMessageKey("Registered");
                $this->loginView->redirect($newlyRegistered);
            }

        } else {

            $currentUser = $this->loginView->getUserClient();

            $isLoggedIn = $this->loginModel->isLoggedIn($currentUser);

            if ($isLoggedIn && $this->loginView->userWantsToLogOut()) {

                $this->loginView->setMessageKey("ByeBye");
                $this->logOut();

            } else if (!$isLoggedIn && $this->loginView->userTriedToLogin()) {

                $this->tryLogin($currentUser);

            }
        }
    }

    /**
     * Removes logged in Session, clears cookies,
     * sets a message and redirects with header("Location: ").
     */
    private function logOut()
    {
        $this->loginModel->logOut();
        $this->loginView->forgetUser();
        $this->loginView->redirect();
    }


    private function tryLogin(\model\UserClient $currentUser)
    {

        $attempt = $this->loginView->getUserInput();

        if ($attempt) {
            if ($attempt->isRemembered()) {
                $this->loginModel->logIn($currentUser);
                $this->loginView->setMessageKey("CookieWelcome");
            } else {
                try {
                    $this->loginModel->tryLogin($attempt, $currentUser);

                    if ($attempt->getKeep()) {
                        $this->loginView->setMessageKey("WelcomeRemember");
                        $this->loginView->rememberUser($attempt->getName());
                    } else {
                        $this->loginView->setMessageKey("Welcome");
                    }
                    $this->loginView->redirect();
                } catch (\exceptions\IncorrectCredentialsException $e) {
                    $this->loginView->setMessageKey("Credentials");
                }

            }
        }
    }
}