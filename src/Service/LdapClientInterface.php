<?php


namespace Connected\LdapClient\Service;


use Connected\LdapClient\Enum\LdapClientSearchUserTypesEnum;

interface LdapClientInterface
{
    CONST DOMAIN = "@valority.intra";

    CONST DC = "DC=valority,DC=intra";

    /**
     * LdapClientInterface constructor.
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param int    $port
     * @param null   $DC
     */
    public function __construct(string $dsn, string $username, string $password, int $port = 636, $DC = null);

    /**
     *  Fetch a create a LDAP link
     *
     * @return mixed
     */
    public function getLink();

    /**
     * Send query to the server
     *
     * @param string $query
     * @param string|null $dn
     * @param array  $attributes By default it will always return dn even if not asked for.
     *
     * @return array
     */
    public function query(string $query, $dn = null, $attributes = null): array;

    /**
     * Fetch all groups
     *
     * @param array|null $attributes By default it will always return dn even if not asked for.
     *
     * @return array
     */
    public function getGroups($attributes = null): array;

    /**
     * Fetch all users
     *
     * @param array|null $attributes By default it will always return dn even if not asked for.
     *
     * @return array
     */
    public function getUsers($attributes = null): array;

    /**
     * Search a user
     *
     * @param string                              $search     What we are looking for
     * @param array|LdapClientSearchUserTypesEnum $type       Search type. Must be an LdapClientSearchUserTypesEnum or an array of LdapClientSearchUserTypesEnum.
     * @param string|null                         $dn
     * @param array|null                          $attributes By default it will always return dn even if not asked for.
     *
     * @return array
     */
    public function searchUser(string $search, $type, $dn = null, $attributes = null): array;

    /**
     * Search a group
     *
     * @param string                              $search     What we are looking for
     * @param string|null                         $dn
     * @param array|null                          $attributes By default it will always return dn even if not asked for.
     *
     * @return array
     */
    public function searchGroup(string $search, $dn = null, $attributes = null): array;

    /**
     * Get entries matching specified email (can be an user, a group, etc...).
     *
     * @param string        $email
     * @param string|null   $dn
     * @param array|null    $attributes By default it will always return dn even if not asked for.
     *
     * @return array
     */
    public function getEntriesByEmail(string $email, $dn, $attributes = null): array;

    /**
     * @param string $username
     * @param string $password
     * @param        $dn
     *
     * @return bool
     */
    public function checkCredentials(string $username, string $password, $dn): bool;

    /**
     * @param string        $ldap
     * @param               $dn
     * @param array|null    $attributes By default it will always return dn even if not asked for.
     *
     * @return mixed
     */
    public function getUser(string $ldap, $dn, $attributes = null): array;

    /**
     * @param null $dn
     *
     * @return string
     */
    public function getDn($dn = null): string;
}