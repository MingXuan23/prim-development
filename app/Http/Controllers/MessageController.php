<?php

namespace App\Http\Controllers;

use App\Models\Chatroom;
use App\Models\File;
use Illuminate\Http\Request;
use App\Models\Message;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function chatUser() {
        $data['users'] = User::where('id', '!=', Auth::id())->get();
        $data['authInfo'] = User::find(Auth::id());

        return view('chat.chat-user', $data);
    }

    public function chatPage($friendId) {
        $data['friendInfo'] = User::findOrFail($friendId);
        $data['authInfo'] = User::find(Auth::id());
        $users1 = array('id' => [ Auth::id(), (int)$friendId ]);
        $users2 = array('id' => [ (int)$friendId, Auth::id() ]);
        
        if (!(Chatroom::where('users', '=', json_encode($users1))->exists()) && !(Chatroom::where('users', '=', json_encode($users2))->exists())) {
            $chatroom = new Chatroom();
            $chatroom->users = json_encode($users1);
            $chatroom->created_at = now();
            $chatroom->save();
        }

        $data['chatroom'] = Chatroom::where('users', '=', json_encode($users1))->orWhere('users', '=', json_encode($users2))->pluck('id')->first();
        $data['chats'] = Message::where('chatroom_id', '=', $data['chatroom'])->get();
        $data['attachments'] = File::where('chatroom_id', '=', $data['chatroom'])->get();

        return view('chat.chat-page', $data);
    }

    public function sendMessage(Request $request) {
        $request->validate([
            'chatroom_id' => 'required',
        ]);

        $message = new Message();
        $message->mesej = $request->mesej == '' ? '-' : $request->mesej;
        $message->sender_id = Auth::id();
        $message->chatroom_id = $request->chatroom_id;
        $message->type_id = 1;
        $message->send_at = now();
        $message->delivered_status = 'no';
        $message->seen_status = 'no';
        if ($files = $request->file('file')) {
            $message->type_id = 2;
            $insertfile = new File();
            $profilefile = date('YmdHis') . "." . $files->getClientOriginalName();
            $files->move('uploads/pdf/', $profilefile);

            $insertfile->name = $profilefile;
            $insertfile->chatroom_id = $request->chatroom_id;
            if ($insertfile->save()) {
                if ($message->save()) {
                    return response()->json([
                        'data' => $message,
                        'success' => true,
                        'message' => 'Message stored successfully',
                    ]);
                }
            }
        }
        else {
            if ($request->mesej == '') {
                return response()->json([
                    'data' => $message,
                    'success' => false,
                    'message' => 'No message to send.',
                ]);
            }
            if ($message->save()) {
                return response()->json([
                    'data' => $message,
                    'success' => true,
                    'message' => 'Message stored successfully',
                ]);
            }
        }
    }

    public function getFile($filename) {
        $file = public_path()."/uploads/".$filename;
        $headers = array('Content-Type: application/pdf',);
        return Response::download($file, $filename, $headers);
    }
}
