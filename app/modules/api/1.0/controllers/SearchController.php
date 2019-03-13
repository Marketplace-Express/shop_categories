<?php
/**
 * User: Wajdi Jurry
 * Date: 01/03/19
 * Time: 03:48 م
 */

namespace Shop_categories\Modules\Api\Controllers;


use Shop_categories\Controllers\BaseController;
use Shop_categories\RequestHandler\Category\SearchRequestHandler;
use Shop_categories\Services\SearchService;

/**
 * Class SearchController
 * @package Shop_categories\Modules\Api\Controllers
 * @RoutePrefix("/api/1.0/search")
 */
class SearchController extends BaseController
{

    private function getService(): SearchService
    {
        return $this->service ?? $this->service = new SearchService();
    }

    /**
     * @Get('/autocomplete')
     */
    public function autocompleteAction()
    {
        try {
            /** @var SearchRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->queryStringToObject($this->request->getQuery()), new SearchRequestHandler());
            if (!$request->isValid()) {
                $request->invalidRequest();
            }
            $request->successRequest($this->getService()->autocomplete($request->toArray()));
        } catch (\Throwable $exception) {

        }
    }

    /**
     * @Get('/')
     */
    public function searchAction()
    {
        try {
            /** @var SearchRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->queryStringToObject($this->request->getQuery()), new SearchRequestHandler());
            if (!$request->isValid()) {
                $request->invalidRequest();
            }
            $result = $this->getService()->search($request->toArray());
            $request->successRequest($result);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}