<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ForumUserController extends Controller
{
    // PATCH /forum/users/{user}/ban
    public function ban(User $user)
    {
        if ($user->forum_banned_at) {
            $user->update(['forum_banned_at' => null]);
            $message = __('User unbanned from forum.');
        } else {
            $user->update(['forum_banned_at' => now()]);
            $message = __('User banned from forum.');
        }

        return back()->with('alert', [
            'message' => $message,
            'level'   => 'success',
        ]);
    }
}
