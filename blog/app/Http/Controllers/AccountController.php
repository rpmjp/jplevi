<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        return view('account.show', [
            'user' => $request->user(),
            'comments' => $request->user()->comments()->with('post')->latest()->get(),
        ]);
    }

    /**
     * Deletion that actually deletes.
     *
     * Comments go with the account rather than being reassigned to a ghost
     * user, because someone asking to be removed did not mean "mostly".
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        abort_if($user->hasAnyRole(['admin', 'editor', 'author']), 403,
            'Accounts that publish cannot be deleted from here.');

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('blog.index')->with('status', 'Your account and comments are gone.');
    }
}
