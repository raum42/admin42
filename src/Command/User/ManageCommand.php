<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Command\User;

use Admin42\Model\User;
use Admin42\TableGateway\UserTableGateway;
use Core42\Command\AbstractCommand;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Validator\EmailAddress;

class ManageCommand extends AbstractCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $shortName
     * @return $this
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * @param array $payload
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @param array $values
     */
    public function hydrate(array $values)
    {
        $this->setEmail((array_key_exists('email', $values)) ? $values['email'] : null);
        $this->setPassword((array_key_exists('password', $values)) ? $values['password'] : null);
        $this->setUsername((array_key_exists('username', $values)) ? $values['username'] : null);
        $this->setDisplayName((array_key_exists('displayName', $values)) ? $values['displayName'] : null);
        $this->setShortName(array_key_exists('shortName', $values) ? $values['shortName'] : null);
        $this->setPayload(array_key_exists('payload', $values) ? $values['payload'] : []);
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if (!empty($this->userId)) {
            $this->user = $this->getTableGateway(UserTableGateway::class)->selectByPrimary((int) $this->userId);
        }

        if (!($this->user instanceof User)) {
            $this->addError("user", "invalid user");
        }

        if (empty($this->email)) {
            $this->addError("email", "email can't be empty");
        } else {
            $check = $this->getTableGateway(UserTableGateway::class)->select([
                'email' => $this->email,
                new Operator('id', Operator::OPERATOR_NOT_EQUAL_TO, $this->user->getId())
            ]);
            if ($check->count() > 0) {
                $this->addError("email", "duplicate email");
            }
        }

        $emailValidator = new EmailAddress();

        if (!$emailValidator->isValid($this->email)) {
            $this->addError("email", "invalid email address");
        }

        if (!empty($this->username)) {
            if ($emailValidator->isValid($this->username)) {
                $this->addError("username", "Username can't be an email");
            } else {
                $check = $this->getTableGateway(UserTableGateway::class)->select([
                    'username' => $this->username,
                    new Operator('id', Operator::OPERATOR_NOT_EQUAL_TO, $this->user->getId())
                ]);
                if ($check->count() > 0) {
                    $this->addError("username", "duplicate username");
                }
            }
        }

        if (!empty($this->password)) {
            $bCrypt = new Bcrypt();
            $this->password = $bCrypt->create($this->password);
        }

        if (empty($this->shortName)) {
            $this->shortName = strtoupper(substr($this->email, 0, 1));
            if (!empty($this->displayName)) {
                $parts = explode(" ", $this->displayName);
                $this->shortName = strtoupper($parts[0]);
                if (count($parts) > 1) {
                    $this->shortName .= $parts[1];
                }
            }
        }
    }


    /**
     *
     */
    protected function execute()
    {
        $this->user->setEmail($this->email)
                    ->setShortName($this->shortName)
                    ->setPayload($this->payload)
                    ->setUsername(!empty($this->username) ? $this->username : null)
                    ->setDisplayName(!empty($this->displayName) ? $this->displayName : null);

        if (!empty($this->password)) {
            $this->user->setPassword($this->password);
        }

        if ($this->user->hasChanged()) {
            $this->user->setUpdated(new \DateTime());
        }

        $this->getTableGateway(UserTableGateway::class)->update($this->user);
    }
}
