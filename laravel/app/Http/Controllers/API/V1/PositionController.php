<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Http\Resources\PositionResource;
use Illuminate\Support\Facades\Cache;
class PositionController extends BaseController
{
    public function getList(Request $request)
    {
        $responseData = Cache::get('positions');
        if (!$responseData) {
            $positions = Position::all();
            $responseData = [
                'positions' => []
            ];
            foreach ($positions as $position) {
                $responseData['positions'][] = new PositionResource($position);
            }
            Cache::put('positions', $responseData, now()->addMinutes(1));
        }

        return response()->json($responseData);
    }
}
