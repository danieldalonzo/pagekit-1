<?php

namespace Pagekit\User\Model;

use Pagekit\Application as App;

trait AccessTrait
{
    /** @Column(type="simple_array") */
    protected $roles = [];

    /**
     * @return int[]
     */
    public function getRoles()
    {
        return (array) $this->roles;
    }

    /**
     * @param int[] $roles
     */
    public function setRoles($roles)
    {
        $this->roles = array_unique($roles);
    }

    /**
     * @param  int $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param  UserInterface $user
     * @return bool
     */
    public function hasAccess(UserInterface $user)
    {
        return !$this->roles or array_intersect($user->getRoles(), $this->roles);
    }

    /**
     * @param  RoleInterface|int $role
     * @return int
     */
    public static function removeRole($role)
    {
        if ($role instanceof RoleInterface) {
            $role = $role->getId();
        }

        $db = self::getConnection();
        $platform = $db->getDatabasePlatform();

        return $db->executeUpdate('UPDATE '.self::getMetadata()->getTable().' SET roles = ('.$platform->getTrimExpression("REPLACE (".$platform->getConcatExpression($db->quote(','), 'roles', $db->quote(',')).", ',{$role},', ',')", 3, $db->quote(',')).' OR NULL)');
    }
}
