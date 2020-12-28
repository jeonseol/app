<?php

declare(strict_types=1);

namespace App\Models;

use DateTime,
    Manju\Helpers\Collection,
    RedBeanPHP\Facade;

/**
 * @required (expire,sid)
 * @property User $user
 * @property Collection $groups
 * @property-read string $sid
 * @property-read DateTime $expire
 */
class Session extends BaseModel {

    const EXPIRE_AFTER = 60 * 60 * 24;

    /**
     * @required
     * @var DateTime
     */
    protected $expire;

    /**
     * @var string
     * @unique
     */
    protected $sid;

    ////////////////////////////   Relations   ////////////////////////////

    /**
     * Set User for session
     * @param User $user
     * @return Session
     */
    public function setUser(User $user): Session {
        if ($id = $user->id) {
            $this->setListOwner($user);
            $this->sid = sha1($user->name . time() . $id);
        }
        return $this;
    }

    /**
     * Get User Assigned to Session
     * @return User|null
     */
    public function getUser(): ?User {
        return $this->getListOwner(User::class);
    }

    /**
     * Gae User Groups assigned to Session
     * @return Collection|null
     */
    public function getGroups(): ?Collection {
        if ($user = $this->getUser()) return $user->groups;
        return null;
    }

    ////////////////////////////   Getters/Setters   ////////////////////////////

    /**
     * Set Expire Date
     * @staticvar Date $converter
     * @param DateTime|int $expire
     * @return Session
     */
    public function setExpire($expire): Session {
        $this->expire = null;
        if (is_int($expire)) $expire = new DateTime(Facade::isoDateTime($expire));
        if ($expire instanceof DateTime) $this->expire = $expire;
        return $this;
    }

    /** @return DateTime */
    public function getExpire(): DateTime {
        return $this->expire;
    }

    /** @return string */
    public function getSid(): string {
        return $this->sid;
    }

    ////////////////////////////   Events   ////////////////////////////

    /**
     * Set expire before writing (if not set already)
     * @staticvar type $ttl
     */
    public function update() {
        static $ttl;
        if ($ttl === null) {
            $cfg = self::getSettings();
            $ttl = $cfg['session.ttl'] ?? self::EXPIRE_AFTER;
        }
        if ($this->expire === null) $this->setExpire(time() + $ttl);
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * @param string $sid
     * @return Session|null
     */
    public static function getSession(string $sid): ?Session {
        if (
                $session = self::findOne(
                        'sid = ?',
                        [$sid]
                )
                and ($session->user instanceof User)
        ) return $session;
        return null;
    }

    /**
     * Removes Expired Sessions from Database
     */
    public static function CleanUp() {

        $now = Facade::isoDateTime();

        foreach (self::find(
                'expire < ?',
                [$now]
        ) as $session) {
            $session->trash();
        }
    }

}
