<?php

namespace App\Repositories\Back;

use App\{
    Models\Ticket,
    Helpers\ImageHelper
};
use App\Helpers\EmailHelper;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;

class TicketRepository
{



    /**
     * Update post.
     *
     * @param  \App\Http\Requests\ImageUpdateRequest  $request
     * @return void
     */

    public function store($request)
    {
        $input = $request->all();
        $input['user_id'] = User::where('email', $request->email)->first()->id;
        if ($request->file('file')) {
            $input['file'] = ImageHelper::handleUploadedImage($request->file('file'), 'files');
        }
        $input['status'] = 'Open';
        $ticket = Ticket::create($input);

        $message = new Message();
        $message->ticket_id = $ticket->id;
        $message->user_id = 0;
        $message->message = $request['message'];
        $message->save();
    
        if (Setting::first()->ticket_mail == 1) {
            $mailData = [
                'to' => $request->email,
                'type' => 'ticket',
                'user_name' => $request->name,
                'body' => __('You got a new message from. ' . Setting::first()->title),
                'subject' => __('Support Ticket'),
            ];

            $emailHelper = new EmailHelper();
            $emailHelper->sendCustomMail($mailData);
        }
    }


    public function create()
    {
        return view('back.ticket.create');
    }

    public function update($ticket, $request)
    {
        $ticket->update(['status' => 'Open']);
        $message = new Message();
        $message->ticket_id = $request['ticket_id'];
        $message->user_id = 0;
        $message->message = $request['message'];
        $message->save();

       
        if (Setting::first()->ticket_mail == 1) {
            $mailData = [
                'to' => $ticket->user->email,
                'type' => 'ticket',
                'body' => __('You got a new message from. ' . Setting::first()->title),
                'subject' => __('Support Ticket Reply'),
            ];

            $emailHelper = new EmailHelper();
            $emailHelper->sendCustomMail($mailData);
        }
    }

    public function delete($ticket)
    {
        $id = $ticket->id;
        if (Ticket::whereId($id)->exists()) {
            $ticket = Ticket::findOrFail($id);
            $messages = $ticket->messages;
            if ($messages->count() > 0) {
                foreach ($messages as $message) {
                    $message->delete();
                }
            }
            if ($ticket->file) {
                ImageHelper::handleDeletedImage($ticket, 'file', 'files');
            }
            $ticket->delete();
        }
    }
}
