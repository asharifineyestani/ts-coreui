<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Statistic;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function __construct()
    {
        Auth::login(User::find(1));

    }


    public function statistics(Request $request, $sort = null)
    {
        $query = Statistic::query()
            ->select(\DB::raw('count( DISTINCT ip) as count '), \DB::raw('Date(created_at) as labels'))
            ->groupBy([\DB::raw('Date(created_at)')])
            ->orderBy('created_at', 'desc');


        switch ($sort) {
            case "month":
                $query = $query
                    ->whereMonth('created_at', now()->month);
            case "year":
                $query = $query
                    ->whereYear('created_at', now()->year);
        }


        $result['count'] = $query->pluck('count');
        $result['labels'] = $query->pluck('labels');


        return $result;


        return $query->pluck('id');

    }
}
