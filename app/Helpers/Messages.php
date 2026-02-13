<?php

namespace App\Helpers;

use App\Utils\RestServiceStatusCode;

class Messages{
    public static array $message=[
        RestServiceStatusCode::SUCCESS_OPERATION=>"Operation effectuée avec succès",
        RestServiceStatusCode::ERROR_RESSOURCE_NOT_FOUND=>"Ressource demandé introuvable",
        RestServiceStatusCode::ERROR_DATA_INVALID=>"Données invalides",
        RestServiceStatusCode::ERROR_RESSOURCE_ALREADY_EXIST=>"Ressource déjà existante",
        RestServiceStatusCode::ERROR_DUPLICATE_RESOURCE=>"Ressource dupliquée",
        RestServiceStatusCode::ERROR_RATE_LIMIT_EXCEED=>"Limite de requêtes dépassée",
        RestServiceStatusCode::ERROR_DELETE_FAILED=>"Suppression échouée",
        RestServiceStatusCode::SERVER_ERROR=>"Erreur serveur",
        RestServiceStatusCode::ERROR_FILE_FORMAT_INVALID=>"Format de fichier invalide",
        RestServiceStatusCode::ERROR_FILE_EXTENSION_INVALID=>"Extension de fichier invalide",
        RestServiceStatusCode::ERROR_FILE_DOWNLOAD_FAILED_BY_URL=>"Échec du téléchargement du fichier par URL",
    ];
}
