<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\User;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService; 
    }

    public function dashboard()
    {
        $leaderboard = $this->leaderboardService->getTopTenLeaderboard();
        return view('admin.dashboard', ['leaderboard' => $leaderboard]);
    }

}