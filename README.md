# LDAP Client

LDAP client est un client qui permet de faire des reqs vers un serveur LDAP sans limitation du nombre de données en retour.

## Installation

```bash
    "repositories": [
        {"type": "vcs", "url": "https://github.com/connected-company/ldap-client"}
    ],
```

Exécutez ensuite
```bash
$ composer require connected-company/ldap-client
```

## Utilisation
Vous avez 2 possibilité:

    - Instencier directement le LdapClient
    
    - Etendre le LdapClient, ce qui vous permet ensuite d'ajouter des méthodes si besoin
    
Dans tout les cas il faut passer en param un DSN, un user et un password au LdapClient (il est possible de changer le port et de DC aussi)

```PHP
$client = new Connected\LdapClient\Service\LdapClient("ldaps://ldap.demo.intra:636", "username", "password");
```

## Méthodes

### getLink(): resource
Retourne le **link identifier** de la connexion LDAP

### query(string $query, ?string $dn = null, ?array $attributes = null): array
Permet d'executer une query vers le serveur LDAP

| Param | Requis | Description |
| ------ | ------ | ------ |
| $query | Oui | Le filtre de la req, ex: **(objectClass=group)**|
| $dn | Non | Permet de modifier le DN, par exemple pour ajouter OU=users|
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

### getGroups(?array $attributes = ["OU","CN","DC"]): array
Permet de récuperer tout les groups

| Param | Requis | Description |
| ------ | ------ | ------ |
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

### getUsers(?array $attributes = null): array
Permet de récuperer tout les users

| Param | Requis | Description |
| ------ | ------ | ------ |
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

### searchUser(string $value, array|LdapClientSearchUserTypesEnum $type = LdapClientSearchUserTypesEnum::FIRSTNAME_LASTNAME, ?string $dn = null, ?array $attributes = null): array
Permet de recher un user

| Param | Requis | Description |
| ------ | ------ | ------ |
| $value | Oui | La valeur a chercher |
| $type | Non | Le type de recherche a faire. Doit être LdapClientSearchUserTypesEnum ou un tableau de LdapClientSearchUserTypesEnum (voir liste des enum ci dessous) |
| $dn | Non | Permet de modifier le DN, par exemple pour ajouter OU=users|
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

Enum **LdapClientSearchUserTypesEnum**

| Param  | Description |
| ------ | ------ |
| ::FIRSTNAME | Permet de chercher sur le champ prenom | 
| ::LASTNAME | Permet de chercher sur le champ nom | 
| ::FIRSTNAME_LASTNAME | Permet de chercher sur les champs prenom et nom |
| ::LDAP | Permet de chercher par LDAP |


### searchUser searchGroup(string $value, ?string $dn = null, ?array $attributes = null): array
Permet de chercher un group

| Param | Requis | Description |
| ------ | ------ | ------ |
| $value | Oui | La valeur a chercher |
| $dn | Non | Permet de modifier le DN, par exemple pour ajouter OU=users|
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

### function getEntriesByEmail(string $email, ?string $dn = null, ?array $attributes = null): array
Permet de remonté tout les éléments (ex: user, group, etc) qui ont l'email spécifié

| Param | Requis | Description |
| ------ | ------ | ------ |
| $email | Oui | L'email a chercher |
| $dn | Non | Permet de modifier le DN, par exemple pour ajouter OU=users|
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

### getUser(string $ldap, ?string $dn = null, ?array $attributes = null): array
Permet de récuper un user via son LDAP

| Param | Requis | Description |
| ------ | ------ | ------ |
| $ldap | Oui | Le LDAP a chercher |
| $dn | Non | Permet de modifier le DN, par exemple pour ajouter OU=users|
| $attributes | Non | Un tableau d'attributs requis, e.g. array("mail", "sn", "cn"). Notez que le "dn" est toujours retourné, quel que soit le type de l'attribut demandé|

### checkCredentials(string $username, string $password, ?string $dn = null): bool
Permet de verifier les identifiants d'un user

| Param | Requis | Description |
| ------ | ------ | ------ |
| $username | Oui | Le username a tester |
| $password | Oui | Le mot de passe a tester |
| $dn | Non | Permet de modifier le DN, par exemple pour ajouter OU=users|

## Exceptions

| Nom | Description |
| ------ | ------ |
| LdapClientException | Exception de base, arrive si un problème apparait lors de la connexion par exemple | 
| LdapClientNotFoundException | Quand l'entry demandé n'est pas trouvé, par exemple avec le search user | 