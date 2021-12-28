<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    /**
     * get messages
     * @version 1.0.0 - 20211228
     * @author Brenner S. Barboza
     * @param User $user
     * @return Builder
     */
    public function getMessages(User $user): Builder
    {
        return Message::where(function($query) use ($user){
            $query->where(function($subQuery) use ($user){
                $subQuery->where('from_user', '=', Auth::user()->id)
                ->where('to_user', '=', $user->id);
            })
            ->orWhere(function($subQuery) use ($user){
                $subQuery->where('from_user', '=', $user->id)
                ->where('to_user', '=', Auth::user()->id);
            });
        });
    }
}
