<?php

namespace view;


class RegisterView
{

    private static $messageId = 'RegisterView::Message';
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
    private static $register = "RegisterView::Register";
    /**
     * @var MessageView
     */
    private $messageView;

    /**
     * @param MessageView $message
     */
    public function __construct(MessageView $message)
    {
        $this->messageView = $message;
    }

    /**
     * @return string
     */
    public function response()
    {

        $message = $this->messageView->getMessage();

        $response = $this->generateRegisterFormHTML($message);

        return $response;
    }

    /**
     * @param $message
     *
     * @return string
     */
    public function generateRegisterFormHTML($message)
    {
        $name = isset($_POST[ self::$name ]) ? filter_var($_POST[ self::$name ], FILTER_SANITIZE_STRING) : "";

        return '<h2>Register new user</h2>
        <form method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Register a new user - Write username and password</legend>
                <p id="' . self::$messageId . '">' . $message . '</p>
                <label for="' . self::$name . '">Username :</label>
                <input type="text" size="20" name="' . self::$name . '" id="' . self::$name . '" value="' . $name . '" />
                <br />
                <label for="' . self::$password . '">Password :</label>
                <input type="password" size="20" name="' . self::$password . '" id="' . self::$password . '" value="" />
                <br />
                <label for="' . self::$passwordRepeat . '">Repeat password :</label>
                <input type="password" size="20" name="' . self::$passwordRepeat . '" id="' . self::$passwordRepeat . '" value="" />
                <br />
                <input id="submit" type="submit" name="' . self::$register . '" value="Register" />
                <br />
            </fieldset>
        </form>';
    }

    /**
     * @return bool
     */
    public function userTriedToRegister()
    {
        if (isset($_POST[ self::$register ])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool|\model\RegisterAttemptModel
     */
    public function getUserInput()
    {
        $name = isset($_POST[ self::$name ]) ? $_POST[ self::$name ] : "";
        $password = isset($_POST[ self::$password ]) ? $_POST[ self::$password ] : "";
        $passwordRepeat = isset($_POST[ self::$passwordRepeat ]) ? $_POST[ self::$passwordRepeat ] : "";

        try {

            $attempt = new \model\RegisterAttemptModel($name, $password, $passwordRepeat);

            return $attempt;

        } catch (\exceptions\UsernameLengthException $e) {

            $this->messageView->setMessageKey("UsernameLength");
        } catch (\exceptions\PasswordLengthException $e) {
            $this->messageView->setMessageKey("PasswordLength");
        } catch (\exceptions\PasswordNotMatchingException $e) {
            $this->messageView->setMessageKey("PasswordMatch");
        } catch (\exceptions\InvalidCharactersException $e) {
            $this->messageView->setMessageKey("InvalidCharacters");
        } catch (\exceptions\PassAndNameLengthException $e) {
            $this->messageView->setMessageKey("PassAndNameLength");
        }

        return false;

    }

    /**
     * @param string $key
     */
    public function setMessageKey($key)
    {
        $this->messageView->setMessageKey($key);
    }
}