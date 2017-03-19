<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdherentControllerTest extends WebTestCase
{

    /**
     * La valeur correspondant à un identifiant valide à utiliser pour les tests
     *
     * @var int
     */
    const IDENTIFIANT_VALIDE = 1;

    /**
     * La valeur correspondant à un identifiant inconnu à utiliser pour les tests
     *
     * @var int
     */
    const IDENTIFIANT_INCONNU = 999;

    /**
     * Le client de test utilisé pour naviguer sur le site
     *
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * On effectue les initialisations communes à tous les tests
     * Ici on initialise le client
     *
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Test de la méthode AdherentController#getAllAction()
     *
     */
    public function testGetAllAction()
    {
        // On teste si l'on a bien un code de retour HTTP 200
        $this->client->request('GET', '/adherents');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // On teste que l'on obtient bien une liste d'adhérents
        $json = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(isset($json['data']['list']), "Doit retourner une liste d'adhrérents");
    }

    /**
     * Test de la méthode AdherentController#testGetByIdAction($id)
     *
     */
    public function testGetByIdAction()
    {
        // On teste si l'on a bien un code de retour HTTP 200 si l'identifiant est connu
        $this->client->request('GET', '/adherents/' . self::IDENTIFIANT_VALIDE);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // On teste si l'on a bien un code de retour HTTP 404 si l'identifiant n'est pas connu
        $this->client->request('GET', '/adherents/' . self::IDENTIFIANT_INCONNU);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), 'Doit retourner une Response 404 pour un adherent inexistant');
    }
}
