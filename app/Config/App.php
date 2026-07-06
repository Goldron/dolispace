<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * URL de base du site
     * --------------------------------------------------------------------------
     *
     * URL racine de votre CodeIgniter. En général, il s'agit de votre URL de base,
     * AVEC un slash final :
     *
     * Ex. : http://example.com/
     */
    public string $baseURL = 'https://localhost:8080/';

    /**
     * Noms d'hôtes autorisés dans l'URL du site, en plus de celui défini dans baseURL.
     * À renseigner si vous acceptez plusieurs noms d'hôtes.
     *
     * Ex. :
     * Si l'URL de votre site ($baseURL) est 'http://example.com/', et que votre site
     * accepte aussi 'http://media.example.com/' et 'http://accounts.example.com/' :
     *     ['media.example.com', 'accounts.example.com']
     *
     * @var list<string>
     */
    public array $allowedHostnames = [];

    /**
     * --------------------------------------------------------------------------
     * Fichier index
     * --------------------------------------------------------------------------
     *
     * En général, il s'agit de votre fichier `index.php`, sauf si vous l'avez renommé.
     * Si votre serveur web est configuré pour supprimer ce fichier des URIs,
     * mettez cette variable à une chaîne vide.
     */
    public string $indexPage = '';

    /**
     * --------------------------------------------------------------------------
     * PROTOCOLE URI
     * --------------------------------------------------------------------------
     *
     * Détermine quelle variable serveur est utilisée pour récupérer la chaîne URI.
     * Le réglage par défaut 'REQUEST_URI' fonctionne sur la plupart des serveurs.
     * Si vos liens ne semblent pas fonctionner, essayez une autre valeur :
     *
     *  'REQUEST_URI': Utilise $_SERVER['REQUEST_URI']
     * 'QUERY_STRING': Utilise $_SERVER['QUERY_STRING']
     *    'PATH_INFO': Utilise $_SERVER['PATH_INFO']
     *
     * ATTENTION : Si vous utilisez 'PATH_INFO', les URIs seront toujours décodées !
     */
    public string $uriProtocol = 'REQUEST_URI';

    /*
    |--------------------------------------------------------------------------
    | Caractères autorisés dans les URLs
    |--------------------------------------------------------------------------
    |
    | Permet de spécifier quels caractères sont autorisés dans vos URLs.
    | Si quelqu'un tente de soumettre une URL avec des caractères non autorisés,
    | un message d'avertissement s'affichera.
    |
    | Pour des raisons de sécurité, il est FORTEMENT recommandé de restreindre
    | les URLs au minimum de caractères nécessaires.
    |
    | Par défaut, seuls ces caractères sont autorisés : `a-z 0-9~%.:_-`
    |
    | Mettez une chaîne vide pour tout autoriser -- mais seulement si vous êtes fou.
    |
    | La valeur configurée est en réalité un groupe de caractères d'expression
    | régulière, utilisée ainsi : '/\A[<permittedURIChars>]+\z/iu'
    |
    | NE MODIFIEZ PAS CECI SANS COMPRENDRE PLEINEMENT LES CONSÉQUENCES !!
    |
    */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    /**
     * --------------------------------------------------------------------------
     * Locale par défaut
     * --------------------------------------------------------------------------
     *
     * La locale représente approximativement la langue et la région depuis
     * laquelle votre visiteur consulte le site. Elle influence les chaînes de
     * langue et d'autres valeurs (symboles monétaires, formats de nombres, etc.)
     * utilisées pour cette requête.
     */
    public string $defaultLocale = 'fr';

    /**
     * --------------------------------------------------------------------------
     * Négociation de locale
     * --------------------------------------------------------------------------
     *
     * Si true, l'objet Request courant déterminera automatiquement la langue
     * à utiliser en fonction de l'en-tête Accept-Language.
     *
     * Si false, aucune détection automatique ne sera effectuée.
     */
    public bool $negotiateLocale = true;

    /**
     * --------------------------------------------------------------------------
     * Locales supportées
     * --------------------------------------------------------------------------
     *
     * Si $negotiateLocale est true, ce tableau liste les locales supportées
     * par l'application, par ordre de priorité décroissante. Si aucune
     * correspondance n'est trouvée, la première locale sera utilisée.
     *
     * IncomingRequest::setLocale() utilise également cette liste.
     *
     * @var list<string>
     */
    public array $supportedLocales = ['fr','en'];

    /**
     * --------------------------------------------------------------------------
     * Fuseau horaire de l'application
     * --------------------------------------------------------------------------
     *
     * Le fuseau horaire par défaut utilisé dans votre application pour afficher
     * les dates avec le helper date, récupérable via app_timezone().
     *
     * @see https://www.php.net/manual/en/timezones.php pour la liste des fuseaux
     *      horaires supportés par PHP.
     */
    public string $appTimezone = 'Europe/Paris';

    /**
     * --------------------------------------------------------------------------
     * Jeu de caractères par défaut
     * --------------------------------------------------------------------------
     *
     * Détermine quel jeu de caractères est utilisé par défaut dans les méthodes
     * qui en nécessitent un.
     *
     * @see http://php.net/htmlspecialchars pour la liste des jeux de caractères supportés.
     */
    public string $charset = 'UTF-8';

    /**
     * --------------------------------------------------------------------------
     * Forcer les requêtes sécurisées globalement
     * --------------------------------------------------------------------------
     *
     * Si true, toutes les requêtes vers cette application seront forcées
     * à passer par une connexion sécurisée (HTTPS). Si la requête entrante
     * n'est pas sécurisée, l'utilisateur sera redirigé vers la version HTTPS
     * et l'en-tête HTTP Strict Transport Security (HSTS) sera défini.
     */
    public bool $forceGlobalSecureRequests = true;

    /**
     * --------------------------------------------------------------------------
     * IPs de proxy inverse
     * --------------------------------------------------------------------------
     *
     * Si votre serveur est derrière un proxy inverse, vous devez mettre en liste
     * blanche les adresses IP du proxy depuis lesquelles CodeIgniter doit faire
     * confiance aux en-têtes X-Forwarded-For ou Client-IP afin d'identifier
     * correctement l'adresse IP du visiteur.
     *
     * Renseignez une adresse IP ou un sous-réseau avec l'en-tête HTTP correspondant.
     *
     * Exemples :
     *     [
     *         '10.0.1.200'     => 'X-Forwarded-For',
     *         '192.168.5.0/24' => 'X-Real-IP',
     *     ]
     *
     * @var array<string, string>
     */
    public array $proxyIPs = [];

    /**
     * --------------------------------------------------------------------------
     * Politique de sécurité du contenu (CSP)
     * --------------------------------------------------------------------------
     *
     * Active la Content Security Policy de la réponse pour restreindre les sources
     * autorisées pour les images, scripts, CSS, audio, vidéo, etc. Si activé,
     * l'objet Response utilisera les valeurs par défaut du fichier
     * `ContentSecurityPolicy.php`. Les contrôleurs peuvent toujours ajouter
     * des restrictions supplémentaires à l'exécution.
     *
     * Pour mieux comprendre le CSP, consultez :
     *
     * @see http://www.html5rocks.com/en/tutorials/security/content-security-policy/
     * @see http://www.w3.org/TR/CSP/
     */
    public bool $CSPEnabled = false;
}
