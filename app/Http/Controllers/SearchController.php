<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;
use App\Models\Agent;
use App\Models\Billboard;

class SearchController extends Controller
{
    //
    public function search(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if (!$agent) abort(404, 'Agent not found');

        $results = Search::add(Billboard::active()->where(
            'agent_id',
            $agent->id
        ), ['name', 'district.name', 'location', 'address'])
            ->beginWithWildcard()
            ->orderBy('updated_at')
            ->search($request->q);

        return response()->json(
            [
                'message' => 'search results',
                'results' => $results
            ]
        );
    }
}
