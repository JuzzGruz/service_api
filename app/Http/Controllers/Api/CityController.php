<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Files\CitiesInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class CityController extends Controller
{
    protected CitiesInterface $cities;

    /**
     * Получить все города в json
     */
    public function getAll(CitiesInterface $cities) : Response
    {
        $json = $cities->getAll();

        return response($json, 200,[])
            ->header('Cache-Control', 'no-cache, must-revalidate');
    }
}
