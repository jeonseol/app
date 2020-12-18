<?php

namespace App\Models;

use Manju\{
    Exceptions\ValidationError, Helpers\Collection, ORM\Model
};

/**
 * @required (name,email,password)
 * @timestamps
 *
 * @property string $name
 * @property string $email
 * @property-write string $password
 * @property-read bool $admin
 * @property Collection $groups
 */
class User extends Model {

    /**
     * @link https://stackoverflow.com/questions/48345922/reference-password-validation
     */
    const PASSWORD_CHECK = '/^(?=\S{8,})(?=\S*[A-Z])(?=\S*[\d])\S*$/';

    /** @var string */
    protected $name;

    /**
     * @unique
     * @var string
     */
    protected $email;

    /** @var string */
    protected $password;

    ////////////////////////////   G/S   ////////////////////////////

    public function getAdmin(): bool {
        $admin = Group::loadGroup('admin');
        return $admin->hasUser($this);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email) {
        $this->email = null;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this;
        if (self::hasEmail($email)) throw new ValidationError("Email already exists");
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    public function setPassword(string $password) {
        $this->password = null;
        //check password
        if (preg_match(self::PASSWORD_CHECK, $password)) {
            $this->password = self::encodePassword($password);
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

    public function hasGroup(Group $group): bool {
        return $this->getGroups()->hasItem($group);
    }

    ////////////////////////////   Utils   ////////////////////////////

    public function checkPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    public static function encodePassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, [
            "cost" => 11
        ]);
    }

    ////////////////////////////   Events   ////////////////////////////



    public function after_update() {

        if ($this->getGroups()->count() == 0) {
            if (Group::countEntries() === 0) {
                $admin = new Group();
                $admin->name = "admin";
                $admin->save();
                $user = new Group();
                $user->name = "user";
                $user->save();
            }
            if (self::countEntries() === 1) {
                $admin = Group::loadGroup('admin');
                $this->getGroups()->addItem($admin);
            }
            $user = Group::loadGroup('user');
            $this->getGroups()->addItem($user);
            $this->save();
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
     * Checks if email already in database
     * @param string $email
     * @return type
     */
    private static function hasEmail(string $email) {
        return self::countEntries('email = ?', [$email]) > 0;
    }

}
