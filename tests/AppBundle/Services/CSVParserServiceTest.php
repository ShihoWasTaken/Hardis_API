<?php

namespace Tests\AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CSVParserServiceTest extends KernelTestCase
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
     * @var \AppBundle\Services\CSVParserService
     */
    private $CSVParserService = null;

    /**
     * Méthode exécutée avant chaque test
     */
    public function setUp()
    {
        self::bootKernel();
        $this->CSVParserService = self::$kernel->getContainer()->get('app.csv_parser');
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Teste la méthode getAdherents()
     */
    public function testGetAdherents()
    {
        $adherents = $this->invokeMethod($this->CSVParserService, 'getAdherents');
        $this->assertTrue(isset($adherents['list']), "La fonction getAdherents doit retourner une liste d'adhérents");
    }

    /**
     * Teste la méthode getAdherentById($id)
     */
    public function testGetAdherentById()
    {
        $adherent = $this->invokeMethod($this->CSVParserService, 'getAdherentById', array(self::IDENTIFIANT_VALIDE));
        $this->assertNotNull($adherent, 'La fonction getAdherentById($id) doit retourner un adherent pour un identifiant valide');

        try {

            $adherent = $this->invokeMethod($this->CSVParserService, 'getAdherentById', array(self::IDENTIFIANT_INCONNU));
        } catch (\Exception $e) {
            $adherent = null;
            $this->assertEquals(404, $e->getCode(), 'La fonction getAdherentById($id) doit retourner une exception avec un code 404');
        }
        $this->assertNull($adherent, 'La fonction getAdherentById($id) doit retourner une exception pour un identifiant inconnu');
    }

    /**
     * Teste si le fichier de données est bien trouvé
     */
    public function testGetAbsolutePath()
    {
        $this->assertNotNull($this->invokeMethod($this->CSVParserService, 'getAbsolutePath'), 'La fonction getAbsolutePath() doit retourner un chemin');
    }

}