<?php

namespace App\Models;

use Manju\{
    Helpers\Collection, ORM, ORM\Model
};

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
class User extends Model {

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
        //if (self::hasName($name)) throw new ValidationError("Name $name already exists");
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email) {
        $this->email = null;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this;
        //if (self::hasEmail($email)) throw new ValidationError("Email $email already exists");
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    public function setPassword(string $password) {
        $this->password = null;
        $config = ORM::getContainer()->get('settings');
        $strong = $config->get('db.strongpasswords');
        if ($strong) {
            //check password
            if (preg_match(self::PASSWORD_CHECK, $password)) {
                $this->password = self::encodePassword($password);
            }
        } else $this->password = self::encodePassword($password);

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

    public function addGroup(Group $group) {

        if (!$this->hasGroup($group)) $this->getGroups()->addItem($group);


        return $this;
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
                $admin = Group::create();
                $admin->name = "admin";
                $admin->save();
                $user = Group::create();
                $user->name = "user";
                $user->save();
            }
            if (self::countEntries() === 1) {
                $admin = Group::loadGroup('admin');
                $this->addGroup($admin);
            }
            $user = Group::loadGroup('user');
            $this->addGroup($user);
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
     * @return type
     */
    private static function hasEmail(string $email) {
        return self::countEntries('email = ?', [$email]) > 0;
    }

    /**
     * Checks if email already in database
     * @param string $name
     * @return type
     */
    private static function hasName(string $name) {
        return self::countEntries('name = ?', [$name]) > 0;
    }

}
