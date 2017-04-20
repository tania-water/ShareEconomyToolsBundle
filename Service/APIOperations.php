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
    /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */

    protected $validator;

    /* @var $translator \Symfony\Component\Translation\TranslatorInterface */
    protected $translator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator = null, $translator)
    {
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * set the object public variables from anotherObject if you pass a doctrine
     * class that extends another class you must implement __get function and
     * throw exception in it or you will face Undefined property notice if you
     * pass a non existing parameter to the bind
     * @param object &$destinationObject
     * @param object $sourceObject
     * @param boolean $readVariablesFromSourceObject
     * @param array $hiddenVariables
     */
    public function bindObjectDataFromObject(&$destinationObject, $sourceObject, $readVariablesFromSourceObject = false, array $hiddenVariables = array())
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $objectVars = array_merge(get_object_vars($readVariablesFromSourceObject ? $sourceObject : $destinationObject), $hiddenVariables);
        foreach ($objectVars as $objectVarName => $value) {
            $variableReadable = false;
            try {
                $variableReadable = $accessor->isReadable($sourceObject, $objectVarName);
            } catch (\Exception $e) {

            }
            if ($variableReadable) {
                $varValue = $accessor->getValue($sourceObject, $objectVarName);
                if ($varValue instanceof \DateTime) {
                    $varValue = $varValue->format('Y-m-d H:i:s');
                } elseif (is_object($varValue)) {
                    continue;
                }
                if ($accessor->isWritable($destinationObject, $objectVarName)) {
                    $accessor->setValue($destinationObject, $objectVarName, $varValue);
                }
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
            if (is_string($varValue)) {
                if (false === stripos($objectVarName, 'password')) {
                    $varValue = trim($varValue);
                    if (strlen($varValue) > 0) {
                        if (false !== stripos($objectVarName, 'date') && !is_numeric($varValue)) {
                            try {
                                $dateTimeObject = new \DateTime($varValue);
                                $varValue = $dateTimeObject;
                            } catch (\Exception $ex) {
                            }
                        } elseif (is_numeric($varValue)) {
                            // PHP will internally convert the string to it is correct type for example float or integer to pass the validator type check
                            $temp = $varValue + 0;
                            if (strlen($temp) === strlen($varValue)) {
                                $varValue = $temp;
                            }
                        }
                    }
                }
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
        $errors = $this->validator->validate($object, null, $groups);

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
     * @param string|null $message
     * @return JsonResponse
     */
    public function getErrorJsonResponse($message = null)
    {
        $errorResponse = new APIResponse\InternalServerError();
        if ($message) {
            $errorResponse->message = $message;
        } else {
            $errorResponse->message = $this->translator->trans('Something is not right, Please contact the development team.');
        }
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @param string|null $message
     * @return JsonResponse
     */
    public function getNotFoundErrorJsonResponse($message = null)
    {
        $errorResponse = new APIResponse\NotFound();
        if ($message) {
            $errorResponse->message = $message;
        } else {
            $errorResponse->message = $this->translator->trans('Not found.');
        }
        return $this->getJsonResponseForObject($errorResponse);
    }

    /**
     * @return JsonResponse
     */
    public function getInvalidAPIKeyJsonResponse()
    {
        return $this->getJsonResponseForObject(new APIResponse\InvalidAPIKey());
    }

    /**
     * @param string|null $message
     * @return JsonResponse
     */
    public function getSuccessJsonResponse($message = null)
    {
        $successResponse = new APIResponse\Success();
        if ($message) {
            $successResponse->message = $message;
        } else {
            $successResponse->message = $this->translator->trans('Success.');
        }
        return $this->getJsonResponseForObject($successResponse);
    }

    /**
     * @param string|null $message
     * @return JsonResponse
     */
    public function getAccessDeniedJsonResponse($message = null)
    {
        $accessDeniedResponse = new APIResponse\AccessDenied();
        if ($message) {
            $accessDeniedResponse->message = $message;
        } else {
            $accessDeniedResponse->message = $this->translator->trans('Access denied.');
        }
        return $this->getJsonResponseForObject($accessDeniedResponse);
    }

    /**
     * @param object $object
     * @return JsonResponse
     */
    public function getJsonResponseForObject($object)
    {
        return new JsonResponse($object);
    }

    /**
     * @param object $object
     * @return array
     */
    public function getObjectDataAsArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
     * @param string $url
     * @return mixed boolean false if we could not get the url content or the url content
     */
    public function getUrlContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $urlContnet = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode === 200 ? $urlContnet : false;
    }
}
