<?php

namespace App\Http\Controllers;

use App\Events\Chat\SendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;

class ChatController extends Controller
{
    public function index()
    {
        $messagesNotRead = Message::selectRaw('from_user, count(*) as total')
        ->where('to_user', '=', Auth::user()->id)
        ->whereNull('receive_message')
        ->groupBy('from_user')
        ->pluck('total', 'from_user')->toArray();

        $users = User::all()->except(Auth::user()->id);
        return view('pages.users.users', ['users' => $users, 'messagesNotRead' => $messagesNotRead]);
    }

    public function chat($id)
    {
        $user = User::find($id);

        $messages = (new Message())->getMessages($user)
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

        Event::dispatch(new SendMessage($message));

        return Response::json([
            'success' => true,
            'message' => $this->message->success("Sua mensagem foi enviada com sucesso")->render()
        ]);
    }

    public function markMessagesAsRead(Request $request)
    {
        $messsageIds = $request->messsageIds;
        Message::whereIn('id', $messsageIds)->update(['receive_message' => date('Y-m-d H:i:s')]);

        return Response::json([
            'success' => true,
            'message' => $this->message->success("As mensagens foram marcadas como lidas")->render()
        ]);
    }

    public function getMessages($userId, $lastId)
    {
        $user = User::find($userId);
        
        $messages = (new Message())->getMessages($user)
                        ->where('id', '<', $lastId)
                        ->orderBy('id', 'DESC')->limit(20)->get()->toArray();


        usort($messages, function($messageA, $messageB){
            $dateOfMessageA = new \DateTime($messageA['created_at']);
            $dateOfMessageB = new \DateTime($messageB['created_at']);

            return $dateOfMessageA->getTimestamp() > $dateOfMessageB->getTimestamp() ? -1 : 1;
        });

        return Response::json(['messages' => $messages]);
    }
}
