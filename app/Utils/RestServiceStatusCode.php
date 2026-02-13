<?php
namespace App\Utils;

class RestServiceStatusCode {
    // Codes de succès (7000-7999)
    public const SUCCESS_OPERATION = 7000;
    public const SUCCESS_GET_TOKEN = 7001;
    public const SUCCESS_OTP_SENT = 7002;
    public const SUCCESS_ENROLMENT_CODE = 7003;
    public const SUCCESS_SENT_VERIFICATION_EMAIL = 7033;
    public const SUCCESS_PASSWORD_RESET = 7034;

    // Erreurs d'authentification et d'autorisation (8000-8009)
    public const FAILED_OPERATION = 8000;
    public const SEE_OTHER_REDIRECT = 8003;
    public const ERROR_FILE_FORMAT_INVALID = 8013;
    public const ERROR_FILE_EXTENSION_INVALID = 8014;
    public const ERROR_FILE_DOWNLOAD_FAILED_BY_URL = 8015;
    public const ERROR_RESSOURCE_NOT_FOUND = 8016;
    public const VALIDATION_ERROR = 8017;
    public const ERROR_OTP_GENERATION_FAILED = 8018;
    public const ERROR_ACCESS_DENIED = 8006;



    // Erreurs liées aux utilisateurs et ressources (8020-8029)
    public const ERROR_RESSOURCE_ALREADY_EXIST = 8019;
    public const ERROR_DUPLICATE_RESOURCE = 8021;
    public const ERROR_DATA_INVALID = 8026;

    // Erreurs diverses
    public const ERROR_RATE_LIMIT_EXCEED = 8444;
    public const ERROR_DELETE_FAILED = 8999;

    // Erreurs serveur (9000-9999)
    public const SERVER_ERROR = 9000;
}
