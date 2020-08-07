<?php
namespace app\modules\api\controllers;

use app\common\requestHandler\FetchRequestHandler;
use app\common\requestHandler\MutationResolver;

/**
 * Class CategoryController
 * @package app\modules\api\controllers
 * @RoutePrefix("/api/categories")
 */
class CategoryController extends BaseController
{
    /**
     * @Post('/fetch')
     * @param FetchRequestHandler $request
     */
    public function fetchAction(FetchRequestHandler $request)
    {
        try {
            /** @var FetchRequestHandler $request */
            $request = $this->di->get('jsonMapper')->map($this->request->getJsonRawBody(), $request);
            if (!$request->isValid()) {
                $request->invalidRequest();
            }
            $request->successRequest($request->toArray());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @Post('/mutate')
     * @Put('/mutate')
     * @Delete('/mutate')
     * @param MutationResolver $resolver
     */
    public function mutateAction(MutationResolver $resolver)
    {
        try {
            /** @var MutationResolver $resolver */
            $resolver = $this->di->get('jsonMapper')->map(
                $this->request->getJsonRawBody(),
                $resolver
            );
            if (!$resolver->isValid()) {
                $resolver->invalidRequest();
            }
            $resolver->successRequest($resolver->resolve()->toArray());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }
}
