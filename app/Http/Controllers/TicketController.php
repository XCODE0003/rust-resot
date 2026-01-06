<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Models\Ticket;
use App\Models\Statistic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:support')->only(['support', 'backend_show', 'backend_update', 'backend_isread', 'backend_close', 'backend_update_reply', 'backend_update_question']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('user')->where('user_id', auth()->id())->latest('updated_at')->paginate();

        return view('pages.cabinet.tickets.list', compact('tickets'));
    }

    public function create()
    {
        $tickets = Ticket::with('user')->where('user_id', auth()->id())->latest()->paginate();
        return view('pages.cabinet.tickets.form', compact('tickets'));
    }

    /**
     * Display a listing of the resource for support manager.
     */
    public function support(Request $request)
    {

        $tickets_status = $request->has('status') ? $request->get('status') : '0';
        if ($tickets_status == '3') {
            $tickets = Ticket::with('user')->onlyTrashed()->latest('updated_at')->paginate();
        } elseif ($tickets_status == '1') {
            $tickets = Ticket::with('user')->where('status', '1')->latest('updated_at')->paginate();
        } elseif ($tickets_status == '2') {
            $tickets = Ticket::with('user')->where('status', '0')->latest('updated_at')->paginate();
        } elseif ($tickets_status == '4') {
            $tickets = Ticket::with('user')->where('status', '1')->where('is_read', '0')->latest('updated_at')->paginate();
        } else {
            $tickets = Ticket::with('user')->withTrashed()->latest('updated_at')->paginate();
        }

        return view('backend.pages.tickets.list', compact('tickets', 'tickets_status'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request): RedirectResponse
    {
        $data = $request->validated();

        //Проверяем замутен ли пользователь
        if (auth()->user()->mute == 1) {
            if (strtotime(auth()->user()->mute_date) > strtotime(date('Y-m-d H:i:s'))) {
                $this->alert('danger', __('Вы были замутены по причине') . ' ' . auth()->user()->mute_reason . ' ' . __('до') . ' ' . auth()->user()->mute_date);
                return back();
            }
        }

        //Проверяем, что в течение часа, создано не больше 2 тикетов
        $date = strtotime(date('d-m-Y H:i:s')) - 60*60;
        $date = date('Y-m-d H:i:s', $date);
        $user_tickets = Ticket::where('user_id', auth()->user()->id)->where('created_at', '>', $date)->get();
        $user_tickets_in_hour = count($user_tickets);

        if ($user_tickets_in_hour >= 3) {
            $this->alert('danger', __('Вы не можете отправить больше 3 жалоб в течение часа!'));
            return back();
        }

        if (isset($data['attachment'])) {
            $data['attachment'] = $request->attachment->store('attachments', 'public');
        }

        //Заменяем перенос строки
        $data['question'] = str_replace("\r\n", "<br>", $data['question']);

        $ticket = new Ticket;
        $ticket->fill($data);
        $ticket->uuid = Str::uuid();
        $ticket->user()->associate(auth()->user());
        $ticket->save();

        $user_tickets_in_hour++;
        $count = 3 - $user_tickets_in_hour;

        $msg_html = '<span class="alert-title">' . __('Сообщение отправлено!') . '</span>';
        $msg_html .= '<p>'. __('Вы можете отправить 3 отчета в час.') .'</p>';
        $msg_html .= '<p>'. __('Осталось') .': <span>' . $count . '/3</span></p>';

        $this->alert('success', $msg_html);

        return redirect()->route('tickets');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        if ($ticket->user_id === auth()->id() && !$ticket->trashed() || auth()->user()->can('support')) {
            $ticket->load(['user', 'answerer']);
        } else {
            abort(404);
        }

        $ticket->user_is_read = '1';
        $ticket->save();

        return view('pages.cabinet.tickets.full', compact('ticket'));
    }

    public function backend_show(Ticket $ticket)
    {
        $ticket->load(['user', 'answerer']);

        //Заменяем перенос строки
        $ticket->question = str_replace("<br>", "\r\n", $ticket->question);

        return view('backend.pages.tickets.full', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function backend_update(Request $request, Ticket $ticket)
    {
        if ($request->has('answer')) {

            $history = json_decode($ticket->history);

            if ($request->has('attachment') && $request->attachment !== NULL) {
                $attachment = $request->attachment->store('attachments', 'public');
            }

            $history[] = array(
                "text" => $request->input('answer'),
                "attachment" => isset($attachment) ? $attachment : '',
                "user_id" => auth()->user()->id,
                "user_name" => auth()->user()->name,
                "updated_at" => date('d.m.Y H:i'),
                "type" => 'answer',
            );
            $ticket->history = json_encode($history);
            $ticket->answerer()->associate(auth()->user());
            $ticket->is_read = '1';
            $ticket->user_is_read = '0';
            $ticket->save();
        }

        return back();
    }

    public function backend_isread(Request $request, Ticket $ticket)
    {
        $ticket->is_read = '1';
        $ticket->save();

        return back();
    }

    public function backend_close(Request $request, Ticket $ticket)
    {
        $ticket->status = 0;
        $ticket->save();

        return back();
    }

    public function update(TicketUpdateRequest $request, Ticket $ticket)
    {
        if ($request->has('answer')) {

            $history = json_decode($ticket->history);

            if ($request->has('attachment') && $request->attachment !== NULL) {
                $attachment = $request->attachment->store('attachments', 'public');
            }

            $history[] = array(
                "text" => str_replace("\r\n", "<br>", $request->input('answer')),
                "attachment" => isset($attachment) ? $attachment : '',
                "user_id" => auth()->user()->id,
                "user_name" => auth()->user()->name,
                "updated_at" => date('d.m.Y H:i'),
                "type" => 'question',
            );
            $ticket->history = json_encode($history);

            $ticket->answer = str_replace("\r\n", "<br>", $request->input('answer'));
            $ticket->answerer()->associate(auth()->user());
            $ticket->is_read = '0';
            $ticket->save();
        }

        return back();
    }

    public function backend_update_reply(Request $request, Ticket $ticket)
    {
        if($ticket->history !== NULL) {
            $histories = json_decode($ticket->history);
        }

        $histories_upd = [];
        $index = 0;
        foreach($histories as $history) {
            $index++;
            if ($request->reply_index == $index) {
                $history->text = str_replace("\r\n", "<br>", $request->reply);
            }
            $histories_upd[] = $history;
        }

        $ticket->history = json_encode($histories_upd);
        $ticket->save();

        $this->alert('success', __('Вы успешно обновили сообщение!'));
        return back();
    }

    public function backend_update_question(Request $request, Ticket $ticket)
    {
        //Заменяем перенос строки
        $ticket->question = str_replace("\r\n", "<br>", $request->question);
        $ticket->save();

        $this->alert('success', __('Вы успешно обновили сообщение!'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if (auth()->user()->can('support')) {
            $ticket->forceDelete();
            if ($ticket->attachment) {
                Storage::disk('public')->delete($ticket->attachment);
            }
        } else if (auth()->id() === $ticket->user_id) {
            $ticket->delete();
        }

        return back();
    }

    public function searchPlayer(Request $request)
    {
        if ($request->ajax() && $request->has('search') && $request->has('server')) {

            $search = $request->input('search');
            $server = $request->input('server');
            $players = Statistic::where('server', $server)->where('general', 1)
                ->where(function($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('player_id', $search);
                })
                ->limit(10)->get();

            $players_res = [];
            foreach ($players as $player) {
                $players_res[] = [
                    'player_id' => strval($player->player_id),
                    'name' => $player->name,
                    'avatar' => $player->avatar_url,
                ];
            }

            return response()->json([
                'status' => 'success',
                'result' => $players_res,
            ]);
        }

        abort(403, 'Unauthorized action.');
    }
}