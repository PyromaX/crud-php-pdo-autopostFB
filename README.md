PHP/MySQL CRUD (Add, Edit, Delete, View) using PDO
========
SQL script to create database and tables is present in **database.sql** file.

The configuration database is present in **config.php** file.

Generate access Token & Post in Facebook (V2.11)
========================================

### **1ère étape :**

**1.** Connectez-vous sur le site développeur de facebook

https://developers.facebook.com/apps/

**2.** Ensuite allez sur "Ajouter une app",

![tutoApp_01](http://pyromax.fr/images/tutoApp_01.png)

**3.** Remplissez le formulaire, validez le Captcha et envoyez,

![tutoApp_02](http://pyromax.fr/images/tutoApp_02.png)

**4.** Allez dans l'onglet "Paramètres" et remplissez les champs indiqués,

![tutoApp_03](http://pyromax.fr/images/tutoApp_03.png)

**5.** Ensuite allez sur "Ajouter une plate-forme", et sélectionnez "site Web"

![tutoApp_04](http://pyromax.fr/images/tutoApp_04.png)

**6.** remplissez le dernier champ et enregistrez les modifications

![tutoApp_05](http://pyromax.fr/images/tutoApp_05.png)

**7.** Allez sur l'onglet "Examen des apps" et publiez votre App

![tutoApp_06](http://pyromax.fr/images/tutoApp_06.png)


### **2ème étape :**

**1.** Rajoutez dans votre projet l'API graph en utilisant composer :

```shell
composer require facebook/graph-sdk
```

![tutoScript_01](http://pyromax.fr/images/tutoScript_01.png)

**2.** Créez un fichier **"recupToken.php"** et insérez avec le code ci-dessous,
il va nous permettre de paramétrer le login Facebook :

```php
<?php
session_start();
if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
    die('Vendor directory not found, Please run composer install.');
}
$fb = new Facebook\Facebook([
    'app_id' => "votre_app_id",
    'app_secret' => "votre_secret_app",
    'default_graph_version' => 'v2.11',
]);
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email','publish_pages','manage_pages','publish_actions','user_friends','public_profile','user_posts','pages_manage_cta']; // Optional permissions
$callback = 'http://www.demopost.com/fb-callback.php';
$loginUrl = $helper->getLoginUrl($callback, $permissions);
echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
```

**3.** Créez un autre fichier nommé **"fb-callback"** à la racine que vous avez ciblée dans **"recupToken"** avec le code ci-dessous,
il vous permet de générer un Token associé à votre App' avec les permissions ajouté ci-dessus,
puis il vous renvoie sur l'intégrale des pages facebook auxquels il est associé avec leurs access token,
si vous voulez seulement avoir le Token(basique et longue vie) de votre App, retirez les 2 dernières lignes du fichier

```php
<?php
session_start();

if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
    die('Vendor directory not found, Please run composer install.');
}
$fb = new Facebook\Facebook([
    'app_id' => "votre_app_id",
    'app_secret' => "votre_secret_app",
    'default_graph_version' => 'v2.11',
]);
$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (! isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}

// Logged in
echo '<h3>Access Token</h3>';
var_dump($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
echo '<h3>Metadata</h3>';
var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId('votre_app_id'); // Replace {app-id} with your app id

$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
        exit;
    }
    echo '<h3>Long-lived</h3>';
    var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;

header('Location: https://graph.facebook.com/me/accounts?access_token='.$accessToken->getValue());
die();
```

**4.** Une fois ceci fait, retournez sur le "Tableau de bord" de votre App puis allez sur "Configuration"
dans le cadran intitulé "Autorisez la connexion via Facebook" s'il n'y est pas,
allez dans "+ Ajouter un produit" puis cliquez sur "configurer" dans l'addon "Facebook Login"

![tutoApp_07](http://pyromax.fr/images/tutoApp_07.png)

**5.** Sélectionnez l'onglet "Paramètres" et cochez la case "Connexion OAuth de navigateur intégrée",
et remplissez le champ ciblé, vous pouvez vérifier votre URI de redirection dans le Validateur un peu plus bas,
puis enregistrez les modifications

![tutoApp_08](http://pyromax.fr/images/tutoApp_08.png)

**6.** Enfin exécutons notre petit script afin de générer notre Token,
tout d'abord ouvrez un onglet Web et lancez **recupToken.php** :

![tutoScript_02](http://pyromax.fr/images/tutoScript_02.png)

Puis validez l'association de votre compte à l'App,

![tutoScript_03](http://pyromax.fr/images/tutoScript_03.png)

La suite du script va s'effectuer et vous allez tomber sur une page comme celle-ci avec les App' auquel vous êtes associé

![tutoScript_04](http://pyromax.fr/images/tutoScript_04.png)

Voilà ! Maintenant vous avez l'Access Token des pages qui va vous permettre de publier ou récupérer des informations directement sur votre page,
! Attention ! Toute commande est associée à des permissions spécifiques, voici un lien pour accéder à toutes les autorisations disponibles :
https://developers.facebook.com/docs/facebook-login/permissions

### **3ème étape :**

**1.** Voici un exemple de publication sur une page facebook depuis un script php,
j'ai ici 2 types de post, un qui va envoyer un message et un lien qui sera interprété,
et l'autre un upload d'image avec une description :

Pour connaitre l'id de votre page facebook, accédez a votre page et c'est la suite de chiffre indiqué dans le lien :

![tutoScript_07](http://pyromax.fr/images/tutoScript_07.png)

```php
<?php

session_start();
if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
    die('Vendor directory not found, Please run composer install.');
}
$facebook = new Facebook\Facebook([
    'app_id' => "votre_app_id",
    'app_secret' => "votre_secret_app",
    'default_graph_version' => 'v2.11',
    'fileUpload' => 'true',//optional
]);

$pageID='id_de_votre_page_facebook';
$facebook->setDefaultAccessToken('Access_Token_de_la_Page');

$data = [
    'message' => 'My first post message with PHP',
    'link' => 'https://developers.facebook.com/docs/',
];

$dataMedia = [
    'caption' => 'My first post picture with PHP',
    'url' => 'https://i.imgur.com/mtYYQMb.jpg',
];

$deb = $facebook->post('/'.$pageID.'/feed/', $data);
$debMedia = $facebook->post('/'.$pageID.'/photos/', $dataMedia);

$graphNode = $deb->getGraphNode();
$graphNode = $debMedia->getGraphNode();

$deb = $facebook->get('/'.$pageID);
$debMedia = $facebook->get('/'.$pageID);
```

Vous pouvez exécuter ce script directement depuis la console en utilisant

```shell
php /votrePath/NomDuFichier.php
```

![tutoScript_05](http://pyromax.fr/images/tutoScript_05.png)

On peut vérifier que les posts ont bien été soumis :

![tutoScript_05](http://pyromax.fr/images/tutoScript_06.png)
