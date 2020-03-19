<?php
/**
 * User: Wajdi Jurry
 * Date: 01/03/19
 * Time: 03:48 م
 */

namespace app\modules\api\controllers;


use app\common\requestHandler\category\AutocompleteRequestHandler;
use app\common\requestHandler\category\SearchRequestHandler;

/**
 * Class SearchController
 * @package app\modules\api\controllers
 * @RoutePrefix("/api/search")
 */
class SearchController extends BaseController
{
    /**
     * @Get('/autocomplete')
     * @param AutocompleteRequestHandler $request
     */
    public function autocompleteAction(AutocompleteRequestHandler $request)
    {
        try {
            /** @var AutocompleteRequestHandler $request */
            $request = $this->di->get('jsonMapper')->map($this->queryStringToObject($this->request->getQuery()), $request);
            $request->successRequest($this->di->getAppServices('searchService')->autocomplete($request->toArray()));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @Get('/')
     * @param SearchRequestHandler $request
     */
    public function searchAction(SearchRequestHandler $request)
    {
        try {
            /** @var SearchRequestHandler $request */
            $request = $this->di->get('jsonMapper')->map($this->queryStringToObject($this->request->getQuery()), $request);
            if (!$request->isValid()) {
                $request->invalidRequest();
            }
            $result = $this->di->getAppServices('searchService')->search($request->toArray());
            if (empty($result)) {
                $request->notFound();
            }
            $request->successRequest($result);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}
