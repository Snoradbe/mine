<?php


namespace App\Http\Controllers\Api;


use App\Handlers\Api\PlayTime\PlayTimeHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayTimeController extends Controller
{
    /**
     * Каждые [10] минут наиграного времени, сервер будет посылать запрос на сайт
     * и [10] опыта будет начислено игроку
     *
     * @param Request $request
     * @param $handler
     * @param int $server
     * @return string
     * @throws \Exception
     */
    public function send(Request $request, PlayTimeHandler $handler, int $server)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'minutes' => 'required|integer',
            'exp' => 'required|integer'
        ]);

        $handler->handle(
            $request->get('name'),
            $server,
            (int) $request->get('minutes'),
            (int) $request->get('exp')
        );

        return 'ok';
    }

    /*public function send(Request $request, int $server)
    {
        try {
            $this->validate($request, [
                'player' => 'required|string',
                'minutes' => 'required|integer'
            ]);

            $player = trim($request->post('player'));
            $minutes = (int) $request->post('minutes', 0);

            if (empty($player) || $minutes < 1) {
                return "player($player) or minutes($minutes) is invalid!";
            }

            //event(new PlayTimeEvent($player, $minutes)); //TODO: хмм

            return 'ok';
        } catch (ValidationException $exception) {
            return $exception->validator->errors()->first();
        }
    }*/
}