<?php


namespace Connected\LdapClient\Exception;

use Exception;
use Throwable;

class LdapClientException extends Exception
{
    /**
     * LdapClientException constructor.
     *
     * @param                $message
     * @param                $link
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message, $link, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . "(last ldap_error: ".ldap_error($link)."", $code, $previous);
    }
}