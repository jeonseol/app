<?php

declare(strict_types=1);

namespace App\Models;

use Manju\Helpers\Collection;

/**
 * @required (name,password)
 * @timestamps
 *
 * @property string $name
 * @property string $email
 * @property-write string $password
 * @property-read bool $admin
 * @property Collection $groups
 */
class User extends BaseModel {

    /**
     * @link https://stackoverflow.com/questions/48345922/reference-password-validation
     */
    const PASSWORD_CHECK = '/^(?=\S{8,})(?=\S*[A-Z])(?=\S*[\d])\S*$/';

    /**
     * @unique
     * @var string
     */
    protected $name;

    /**
     * @unique
     * @var string
     */
    protected $email;

    /** @var string */
    protected $password;

    ////////////////////////////   G/S   ////////////////////////////

    /**
     * Checks ic current user has group admin
     * @return bool
     */
    public function getAdmin(): bool {
        $admin = Group::loadGroup('admin');
        return $admin->hasUser($this);
    }

    /**
     * Get Name
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get Email
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Set Name
     * @param string $name
     * @return User
     */
    public function setName(string $name): User {
        $this->name = $name;
        return $this;
    }

    /**
     * Set Email
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User {
        $this->email = null;
        if (self::checkEmailValid($email)) {
            $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        }
        return $this;
    }

    /**
     * Set Password
     * @staticvar type $strong
     * @param string $password
     * @return \App\Models\User
     */
    public function setPassword(string $password): User {
        $this->password = null;
        if (self::checkPasswordValid($password)) {
            $this->password = $this->encodePassword($password);
        }
        return $this;
    }

    ////////////////////////////   Relations   ////////////////////////////

    /**
     * Get List of groups
     * @return Collection
     */
    public function getGroups(): Collection {
        return $this->getSharedList(Group::class);
    }

    /**
     * Checks if user is in the specified group
     * @param Group $group
     * @return bool
     */
    public function hasGroup(Group $group): bool {
        return $this->getGroups()->hasItem($group);
    }

    /**
     * Add a Group to the user
     * @param Group $group
     * @return User
     */
    public function addGroup(Group $group): User {
        if (!$this->hasGroup($group)) $this->getGroups()->addItem($group);
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Checks Email Validity
     * @param string $email
     * @return bool
     */
    public static function checkEmailValid(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Checks if password is valid
     * @staticvar type $strong
     * @param string $password
     * @return bool
     */
    public static function checkPasswordValid(string $password): bool {
        static $strong;
        if (!is_bool($strong)) {
            $cfg = self::getSettings();
            $strong = $cfg['db.strongpasswords'] === true;
        }

        if (
                ($strong === true)
                and preg_match(self::PASSWORD_CHECK, $password) > 0
        ) {
            return true;
        } elseif (
                ($strong === false)
                and!empty($password)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Checks if password matches the current user password
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    /**
     * Encode Password algorythm
     * @param string $password
     * @return string
     */
    protected function encodePassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, [
            "cost" => 11
        ]);
    }

    ////////////////////////////   Events   ////////////////////////////

    public function after_update() {



        if ($this->getGroups()->count() == 0) {
            if (Group::countEntries() === 0) {
                foreach (['admin', 'user'] as $label) {
                    $group = Group::create();
                    $group->name = $label;
                    $group->save(true);
                }
            }
            if (self::countEntries() === 1) {
                if ($group = Group::loadGroup('admin')) $this->addGroup($group);
            }
            if ($group = Group::loadGroup('user')) {
                $this->addGroup($group);
                $this->save();
            }
        }
    }

    ////////////////////////////   Finders   ////////////////////////////

    /**
     * Get user by credentials
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public static function getUser(string $email, string $password): ?User {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return null;
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($user = self::findOne('email = ?', [$email])) {
            if ($user->checkPassword($password) === true) return $user;
        }
        return null;
    }

    /**
     * Get user by name
     *
     * @param string $name
     * @param string $password
     * @return User|null
     */
    public static function getUserByName(string $name, string $password): ?User {
        if ($user = self::findOne('name = ?', [$name])) {
            if ($user->checkPassword($password) === true) return $user;
        }
        return null;
    }

    /**
     * Checks if email already in database
     * @param string $email
     * @return bool
     */
    public static function hasEmail(string $email): bool {
        return self::countEntries('email = ?', [$email]) > 0;
    }

    /**
     * Checks if email already in database
     * @param string $name
     * @return bool
     */
    public static function hasName(string $name): bool {
        return self::countEntries('name = ?', [$name]) > 0;
    }

}
