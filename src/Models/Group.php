<?php

declare(strict_types=1);

namespace App\Models;

use Manju\Helpers\Collection;

/**
 * @required (name)
 *
 * @property string $name
 * @property Collection $users
 */
class Group extends BaseModel {

    /**
     * @var string
     * @required
     * @unique
     */
    protected $name;

    /**
     * @var string
     * @required
     * @unique
     */
    protected $label;

    ////////////////////////////   Getters/Setters   ////////////////////////////

    /**
     * Get Group name
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set Group name
     * @param string $name
     * @return $this
     */
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    ////////////////////////////   Relations   ////////////////////////////

    /**
     * Get User List From Group
     * @return Collection
     */
    public function getUsers(): Collection {
        return $this->getSharedList(User::class);
    }

    /**
     * Check if user in group
     * @param User $user
     * @return bool
     */
    public function hasUser(User $user): bool {
        return $this->getUsers()->hasItem($user);
    }

    /**
     * Adds User to Group
     * @param User $user
     */
    public function addUser(User $user) {
        if (!$this->hasUser($user)) {
            $this->getUsers()->addItem($user);
            $this->save();
        }
        return $this;
    }

    ////////////////////////////   Finders   ////////////////////////////

    /**
     * Find Group by name
     * @param string $name
     * @return Group|null
     */
    public static function loadGroup(string $name): ?Group {
        return self::findOne('name = ?', [$name]);
    }

}
