<?php

namespace Ibtikar\ShareEconomyToolsBundle\Service;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ibtikar\ShareEconomyToolsBundle\APIResponse;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class APIOperations
{

    /** @var $assetsDomain string */
    private $assetsDomain;

    /**
     * @param string $assetsDomain
     */
    public function __construct($assetsDomain)
    {
        $this->assetsDomain = "http://$assetsDomain";
    }

    /**
     * @param ConstraintViolationList $errorsObjects
     * @return JsonResponse
     */
    public function getValidationErrorsJsonResponse(ConstraintViolationList $errorsObjects)
    {
        $errors = array();
        foreach ($errorsObjects as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }
        return $this->getErrorsJsonResponse($errors);
    }

    /**
     * @param array $errors array of "field name" => "error"
     * @return JsonResponse
     */
    public function getErrorsJsonResponse(array $errors)
    {
        $errorResponse = new APIResponse\ValidationErrors();
        $errorResponse->errors = $errors;
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function getErrorResponse($message = null)
    {
        $errorResponse = new APIResponse\InternalServerError();
        if ($message) {
            $errorResponse->message = $message;
        }
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function getNotFoundErrorResponse($message = null)
    {
        $errorResponse = new APIResponse\NotFound();
        if ($message) {
            $errorResponse->message = $message;
        }
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @param object $object
     * @return JsonResponse
     */
    public function getJsonResponseForObject($object)
    {
        return new JsonResponse($this->getObjectDataAsArray($object));
    }

    /**
     * @param object $object
     * @return array
     */
    public function getObjectDataAsArray($object)
    {
        return json_decode(json_encode($object), true);
    }
}
