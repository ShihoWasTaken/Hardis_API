<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdherentController extends Controller
{
    /**
     * @var \AppBundle\Services\CSVParserService
     */
    private $CSVParser = null;

    /**
     * Retourne une réponse en JSON correspondant à la liste de tout les adhérents
     * Retoune une erreur 404 si aucun adhérent n'est présent dans le fichier à lire
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAction(Request $request)
    {
        $this->CSVParser = $this->container->get('app.csv_parser');
        try {
            $adherents = $this->CSVParser->getAdherents();
            $response = new JsonResponse(
                array(
                    'status_code' => 200,
                    'data' => $adherents
                )
            );
        } catch (\Exception $e) {
            $response = $this->CSVParser->returnExceptionToJSONResponse($e);
        }
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }

    /**
     * Retourne une réponse en JSON correspondant à l'adhérent dont l'id est passé en paramètre, une réponse 404 avec le détail de l'erreur si l'adhérent est inconnu
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function getByIdAction(Request $request, $id)
    {
        $this->CSVParser = $this->container->get('app.csv_parser');
        try {
            $adherent = $this->CSVParser->getAdherentById($id);
            $response = new JsonResponse(
                array(
                    'status_code' => 200,
                    'data' => $adherent
                )
            );
        } catch (\Exception $e) {
            $response = $this->CSVParser->returnExceptionToJSONResponse($e);
        }
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $response;
    }
}
