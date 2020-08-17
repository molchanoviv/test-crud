<?php

declare(strict_types=1);

namespace App\View;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * App\View\JsonStructureHandler
 */
class JsonStructureHandler
{
    /**
     * @throws \JsonException
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request): Response
    {
        $data = $view->getData();
        $statusCode = $view->getStatusCode() ?? Response::HTTP_OK;
        if (Response::HTTP_OK === $statusCode && !$data instanceof Form) {
            $result = $data;
            $view->setData($result);

            return $handler->createResponse($view, $request, 'json');
        }
        $statusCode = $view->getStatusCode() ?? Response::HTTP_BAD_REQUEST;
        if ($data instanceof \Throwable) {
            $result = json_decode($data->getMessage(), true, 512, JSON_THROW_ON_ERROR);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = [
                    'code' => $statusCode,
                    'message' => $data->getMessage(),
                ];
            } else {
                $data = array_merge(['code' => $statusCode], $result);
            }
        }
        if ($data instanceof Form) {
            $children = $data->getErrors(true, true)->getChildren();
            $data = [
                'code' => $statusCode,
                'message' => false !== $children ? $children->getMessage() : 'Form is invalid',
            ];
        }
        $result = [
            'status' => $statusCode,
            'error' => $data,
        ];
        $view->setStatusCode($statusCode);
        $view->setData($result);

        return $handler->createResponse($view, $request, 'json');
    }
}
