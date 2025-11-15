<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class UserAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_if($user->id !== Auth::id(), 403, 'You are not authorized to access this profile.');

        return Inertia::render('UserAccount/IndexPage', [
            'user' => $user->only('name', 'email', 'location'),
        ]);
    }


    public function session(Request $request)
    {
        $user = Auth::user();
        $sessions = [];

        if (config('session.driver') === 'database') {
            $sessionRecords = DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($sessionRecords as $session) {
                $sessions[] = [
                    'id' => $session->id ?? '',
                    'agent' => $this->formatAgent($session->user_agent ?? ''),
                    'ip' => $session->ip_address ?? '',
                    'lastActive' => $session->last_activity ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans() : '',
                    'isCurrent' => ($session->id ?? '') === $request->session()->getId(),
                ];
            }
        }

        return Inertia::render('UserAccount/IndexSessionPage', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }


    protected function formatAgent($userAgent)
    {
        if (empty($userAgent)) {
            return ['device' => 'Unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        return [
            'device' => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
            'platform' => $agent->platform() ?: 'Unknown',
            'browser' => $agent->browser() ?: 'Unknown',
        ];
    }
}