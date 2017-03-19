<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CSVParserService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface;
     */
    private $container;

    /**
     * Constructeur de CSVParserService. Injecte les conteneurs dans le service.
     * @param ContainerInterface $container Les conteneurs de l'application
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retourne les informations de l'exception survenu au format JSON
     *
     * @param \Exception $e L'exception à transformer en JSON
     * @return JsonResponse Une réponse JSON contenant les informations de l'Exception
     */
    public function returnExceptionToJSONResponse(\Exception $e)
    {
        $response = new JsonResponse(
            array(
                'status_code' => $e->getCode(),
                'error' => $e->getMessage()
            )
        );
        $response->setStatusCode($e->getCode());
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }

    /**
     * Retourne un tableau indéxés par l'id des adhérents et contenant un tableau associatif contenant les informations de l'adhérent, si aucun adhérent n'est trouvé renvoie une Exception avec un code d'erreur 404
     *
     * @return array
     * @throws \Exception Exception indiquant que aucun adhérent n'a été trouvé et retournant un code d'erreur 404
     */
    public function getAdherents()
    {
        $adherents = $this->parseCSV();
        if (empty($adherents)) {
            throw new \Exception("Aucun adhérent n’est présent", 404);
        }
        return $this->getAhdherentsSortedByLastNameAndFirstName(array_values($adherents));
    }

    /**
     * Retourne un tableau associatif contenant les informations de l'adhérent, si l'adhérent n'existe pas renvoie une Exception avec un code d'erreur 404
     *
     * @param string $id L'identifiant de l'adhérent
     * @return mixed
     * @throws \Exception Exception indiquant que aucun adhérent n'a été trouvé pour cet identifiant et retournant un code d'erreur 404
     */
    public function getAdherentById($id)
    {
        $adherents = $this->parseCSV();
        if (!isset($adherents[$id])) {
            throw new \Exception("Aucun adhérent ne correspond à votre demande", 404);
        }
        return $adherents[$id];
    }

    /**
     * Retourne les adhérents triés par nom et prénom croissant
     *
     * @param array $data Le tableau non trié
     * @return array Le tableau trié par nom et prenom croissant
     */
    private function getAhdherentsSortedByLastNameAndFirstName(array $data)
    {
        foreach ($data as $key => $row) {
            $lastName[$key] = $row['nom'];
            $firstName[$key] = $row['prenom'];
        }

        array_multisort($lastName, SORT_ASC, $firstName, SORT_ASC, $data);
        return array('list' => $data, 'count' => count($data));
    }

    /**
     * Retourne le chemin absolu du fichier de données
     *
     * @return mixed
     * @throws \Exception Une exception indiquant un code d'erreur 500 indiquant que le fichier est manquant
     */
    private function getAbsolutePath()
    {
        $kernel = $this->container->get('kernel');
        try {
            $path = $kernel->locateResource('@AppBundle/Resources/public/' . $this->container->getParameter('datasrc'));
        } catch (\InvalidArgumentException $e) {
            throw new \Exception("Le fichier d’entrée est introuvable", 500);
        }
        return $path;
    }

    /**
     * Récupère les informations brutes du fichier CSV et les indexes de façon à les rendre facilement accesibles
     *
     * @param array $rows Tableau contenant les informations lues dans le fichier CSV
     * @return array Les adhérents sous forme de tableau avec comme clé l'idenfiant et comme valeur un tableau des informations de l'adhérent sous la forme clé => valeur
     */
    private function formatRows(array $rows)
    {
        $adherents = array();
        foreach ($rows as $row) {
            $adherent = array(
                'id' => $row[0],
                'nom' => $row[1],
                'prenom' => $row[2],
                'telephone' => $row[3],
            );
            $adherents[$row[0]] = $adherent;
        }
        return $adherents;
    }

    /**
     * Parse un fichier CSV et retourne les lignes sous forme de tableau avec comme clé l'identifiant de l'adhérent
     *
     * @return array
     */
    private function parseCSV()
    {
        $ignoreFirstLine = true;

        $rows = array();
        if (($handle = fopen($this->getAbsolutePath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                $i++;
                if ($ignoreFirstLine && $i == 1) {
                    continue;
                }
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $this->formatRows($rows);
    }
}
