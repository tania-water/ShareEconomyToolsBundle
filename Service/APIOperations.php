<?php

namespace Ibtikar\ShareEconomyToolsBundle\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ibtikar\ShareEconomyToolsBundle\APIResponse;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class APIOperations
{

    /** @var ValidatorInterface $validator */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
    }

    /**
     * set the object public variables from anotherObject
     * @param object &$destinationObject
     * @param object $sourceObject
     */
    public function bindObjectDataFromObject(&$destinationObject, $sourceObject)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $objectVars = get_object_vars($destinationObject);
        foreach ($objectVars as $objectVarName => $value) {
            if ($accessor->isReadable($sourceObject, $objectVarName)) {
                $varValue = $accessor->getValue($sourceObject, $objectVarName);
                if (strlen($varValue) > 0 && is_numeric($varValue)) {
                    // PHP will internally convert the string to it is correct type for example float or integer to pass the validator type check
                    $varValue = $varValue + 0;
                }
                $accessor->setValue($destinationObject, $objectVarName, $varValue);
            }
        }
    }

    /**
     * set the object public variables from the request
     * @param object &$object
     * @param Request $request
     */
    public function bindObjectDataFromRequst(&$object, Request $request)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $objectVars = get_object_vars($object);
        foreach ($objectVars as $objectVarName => $value) {
            $varValue = $request->get($objectVarName, $value);
            if (strlen($varValue) > 0 && is_numeric($varValue)) {
                // PHP will internally convert the string to it is correct type for example float or integer to pass the validator type check
                $varValue = $varValue + 0;
            }
            $accessor->setValue($object, $objectVarName, $varValue);
        }
    }

    /**
     * set the object public variables from the request and then validate the object
     * @param object &$object
     * @param Request $request
     * @param array $validationGroups
     * @return JsonResponse|null errors response or null if no errors found
     */
    public function bindAndValidateObjectDataFromRequst(&$object, Request $request, array $validationGroups = array('Default'))
    {
        $this->bindObjectDataFromRequst($object, $request);
        $errorsObjects = $this->validator->validate($object, null, $validationGroups);
        if (count($errorsObjects) > 0) {
            return $this->getValidationErrorsJsonResponse($errorsObjects);
        }
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
     * validate object and return error messages array
     *
     * @param type $object
     * @param type $groups
     * @return array
     */
    public function validateObject($object, $groups = null)
    {
        $validationMessages = [];
        $errors             = $this->validator->validate($object, null, $groups);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $validationMessages[$error->getPropertyPath()] = $error->getMessage();
            }
        }

        return $validationMessages;
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
    public function getSingleErrorJsonResponse($message)
    {
        $errorResponse = new APIResponse\Fail();
        $errorResponse->message = $message;
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function getErrorJsonResponse($message = null)
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
    public function getNotFoundErrorJsonResponse($message = null)
    {
        $errorResponse = new APIResponse\NotFound();
        if ($message) {
            $errorResponse->message = $message;
        }
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function getInvalidAPIKeyJsonResponse()
    {
        return $this->getJsonResponseForObject(new APIResponse\InvalidAPIKey());
    }

    /**
     * @return JsonResponse
     */
    public function getSuccessJsonResponse()
    {
        return $this->getJsonResponseForObject(new APIResponse\Success());
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
