<?php

namespace Connected\LdapClient\Service;

use Connected\LdapClient\Enum\LdapClientSearchUserTypesEnum;
use Connected\LdapClient\Exception\LdapClientException;
use Connected\LdapClient\Exception\LdapClientNotFoundException;

class LdapClient implements LdapClientInterface
{
    /** @var string */
    private $dsn;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var int */
    private $port;

    /** @var false|resource */
    private $linkId;

    /** @var string */
    private $DC;

    /**
     * @inheritDoc
     */
    public function __construct(string $dsn, string $username, string $password, int $port = 636, $DC = null)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;

        if (is_null($DC)) {
            $this->DC = self::DC;
        } else {
            $this->DC = $DC;
        }
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function getLink()
    {
        if ($this->linkId === null || $this->linkId === false) {
            $this->linkId = ldap_connect($this->dsn, $this->port);

            ldap_set_option($this->linkId, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->linkId, LDAP_OPT_REFERRALS, 0);

            if (!ldap_bind($this->linkId, $this->username . self::DOMAIN, $this->password)) {
                throw new LdapClientException("Cannot bind LDAP connection", $this->linkId);
            }
        }

        return $this->linkId;
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function query(string $query, $dn = null, $attributes = null): array
    {
        $ds = $this->getLink();

        $pageSize = 100;
        $cookie = '';

        $results = [];

        do {
            /**
             * Deprecated but no replacement..
             * @noinspection PhpDeprecationInspection
             */
            ldap_control_paged_result($ds, $pageSize, true, $cookie);

            $result  = ldap_search($ds, $this->getDn($dn), $query, $attributes);
            $entries = @ldap_get_entries($ds, $result);

            foreach ($entries as $e) {
                $results[] = $e;
            }

            /**
             * Deprecated but no replacement..
             * @noinspection PhpDeprecationInspection
             */
            ldap_control_paged_result_response($ds, $result, $cookie);

        } while($cookie !== null && $cookie != '');

        return $results;
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function getGroups($attributes = ["OU","CN","DC"]): array
    {
        return $this->query("(objectClass=group)", null, $attributes);
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function getUsers($attributes = null): array
    {
        return $this->query("(objectClass=group)", null, $attributes);
    }

    /**
     * @param        $type
     * @param string $value
     *
     * @return string
     */
    private function getQueryFilterFromSearchType($type, string $value): string
    {
        $filterToReturn = "";
        if (is_string($type)) {
            if ($type === LdapClientSearchUserTypesEnum::FIRSTNAME_LASTNAME) {
                $filterToReturn .= "(cn=*" . $value . "*)";
            } elseif ($type === LdapClientSearchUserTypesEnum::LASTNAME) {
                $filterToReturn .= "(sn=*" . $value . "*)";
            } elseif ($type === LdapClientSearchUserTypesEnum::FIRSTNAME) {
                $filterToReturn .= "(givenName=*" . $value . "*)";
            } elseif ($type === LdapClientSearchUserTypesEnum::LDAP) {
                $filterToReturn .= "(sAMAccountName=*" . $value . "*)";
            }
        } elseif (is_array($type)) {
            foreach ($type as $enum) {
                $filterToReturn .= $this->getQueryFilterFromSearchType($enum, $value);
            }
        }

        return $filterToReturn;
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function searchUser(string $value, $type = LdapClientSearchUserTypesEnum::FIRSTNAME_LASTNAME, $dn = null, $attributes = null): array
    {
        $query = "(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2))";

        $query .= $this->getQueryFilterFromSearchType($type, $value);

        $query .= ")";

        return $this->query($query, $dn, $attributes);
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function searchGroup(string $value, $dn = null, $attributes = null): array
    {
        $query = "(&(objectclass=group)(cn=$value))";

        return $this->query($query, $dn, $attributes);
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function getEntriesByEmail(string $email, $dn = null, $attributes = null): array
    {
        return $this->query("(|(mail=" . $email .")(ProxyAddresses=smtp:" . $email . "))", $dn, $attributes);
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function getUser(string $ldap, $dn = null, $attributes = null): array
    {
        $query = "(&(objectClass=user)(objectCategory=person)(sAMAccountName=" . $ldap . "))";

        $result = $this->query($query, $dn, $attributes);

        if (count($result) > 0) {
            return $result[0];
        } else {
            throw new LdapClientNotFoundException("$ldap not found", $this->getLink());
        }
    }

    /**
     * @inheritDoc
     *
     * @throws LdapClientException
     */
    public function checkCredentials(string $username, string $password, $dn = null): bool
    {
        $link = @ldap_connect($this->dsn, $this->port);
        if ($link) {
            return @ldap_bind($link, $username . self::DOMAIN, $password);
        } else {
            throw new LdapClientException("Could not contact server with ldap_connect", $link);
        }
    }

    /**
     * @inheritDoc
     */
    public function getDn($dn = null): string
    {
        if (is_null($dn)) {
            return self::DC;
        } else {
            return $dn.",".self::DC;
        }
    }
}