<?php

declare(strict_types=1);

namespace App\Models;

use Manju\{
    Helpers\Collection, ORM, ORM\Model
};

/**
 * @required (expire,sid)
 * @property User $user
 * @property Collection $groups
 * @property-read string $sid
 * @property-read int $expire
 */
class Session extends Model {

    const EXPIRE_AFTER = 60 * 60 * 24;

    /** @var int */
    protected $expire;

    /**
     * @var string
     * @unique
     */
    protected $sid;

    public function setUser(User $user) {

        if ($id = $user->id) {
            $settings = ORM::getContainer()->get('settings');
            $expire = $settings->get('session.ttl') ?? self::EXPIRE_AFTER;
            $this->setListOwner($user);
            $this->expire = time() + $expire;
            $this->sid = sha1($user->name . time() . $user->id);
        }
    }

    public function getUser(): ?User {
        return $this->getListOwner(User::class);
    }

    public function getGroups(): ?Collection {

        if ($user = $this->getUser()) return $user->groups;
        return null;
    }

    public function getExpire(): int {
        return $this->expire;
    }

    public function getSid(): string {
        return $this->sid;
    }

    /**
     * @param string $sid
     * @return Session|null
     */
    public static function getSession(string $sid): ?Session {
        if ($session = self::findOne(
                        'sid = ?',
                        [$sid]
                ) and $session->user) return $session;
        return null;
    }

    public static function CleanUp() {
        $now = time();
        foreach (self::find(
                'expire < ?',
                [$now]
        ) as $session) {
            $session->trash();
        }
    }

}
