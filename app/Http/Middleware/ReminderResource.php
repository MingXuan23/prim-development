<?php

namespace App\Http\Middleware;

use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;
use Closure;

class ReminderResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userId = Auth::id();

        if ($request->route('reminder')) {
            $reminder = Reminder::find($request->route('reminder'));

            if ($reminder && $reminder->user_id != $userId) {
                return redirect('/reminder');
            }
        }

        return $next($request);
    }
}
