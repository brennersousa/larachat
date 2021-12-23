<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::all()->except(Auth::user()->id);
        return view('pages.users.users', ['users' => $users]);
    }

    public function chat($id)
    {
        $user = User::find($id);
        
        $messages = Message::where(function($q) use ($user){
            $q->where('from_user', '=', Auth::user()->id)
            ->where('to_user', '=', $user->id);
        })
        ->orWhere(function($q) use ($user){
            $q->where('from_user', '=', $user->id)
            ->where('to_user', '=', Auth::user()->id);
        })
        ->orderBy('id', 'DESC')->limit(20)->get()->toArray();

        usort($messages, function($messageA, $messageB){
            $dateOfMessageA = new \DateTime($messageA['created_at']);
            $dateOfMessageB = new \DateTime($messageB['created_at']);

            return $dateOfMessageA->getTimestamp() < $dateOfMessageB->getTimestamp() ? -1 : 1;
        });
        
        $data = [
            'user' => $user->toArray(),
            'messages' => $messages
        ];
        return json_encode($data);
    }

    public function sendMessage(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user || $user->id == Auth::user()->id){
            return Response::json([
                'success' => false,
                'message' => $this->message->error("Ops, não é possível enviar mensagem para o usuário informado.")->render()
            ]);
        }

        $message = new Message();
        $message->from_user = Auth::user()->id;
        $message->to_user = $user->id;
        $message->message = $request->message;
        
        if(!$message->save()){
            return Response::json([
                'success' => false,
                'message' => $this->message->error("Ops, não foi possível enviar sua mensagem. Tente novamente mais tarde !")->render(),
            ]);
        }

        return Response::json([
            'success' => true,
            'message' => $this->message->success("Sua mensagem foi enviada com sucesso")->render()
        ]);
    }
}
