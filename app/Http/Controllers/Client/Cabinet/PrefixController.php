<?php


namespace App\Http\Controllers\Client\Cabinet;


use App\Exceptions\Exception;
use App\Handlers\Client\Cabinet\ChangePrefixHandler;
use App\Handlers\Client\Cabinet\LoadPrefixSuffixHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Cabinet\Prefix\PrefixSuffix;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PrefixController extends Controller
{
    public function getPrefix(LoadPrefixSuffixHandler $handler, int $server)
    {
        return new JsonResponse($handler->handle(
            Auth::getUser(),
            $server
        )->toArray());
    }

    public function setPrefix(Request $request, ChangePrefixHandler $handler, int $server)
    {
        try {
            $config = CabinetSettings::getPrefixSettings();
            $colors = implode(',', array_keys($config['colors']));

            $this->validate($request, [
                'prefix_color' => 'required|in:' . $colors,
                'prefix' => 'nullable|min:' . $config['min'] . '|max:' . $config['max'] . '|regex:/([' . $config['regex'] . ']+)/',
                'nick_color' => 'required|in:' . $colors,
                'text_color' => 'required|in:' . $colors,
            ]);

            $handler->handle(
                Auth::getUser(),
                new PrefixSuffix(
                    $request->post('prefix_color'),
                    (string) $request->post('prefix', ''),
                    $request->post('nick_color'),
                    $request->post('text_color')
                ),
                $server
            );

            return new JsonResponse([
                'msg' => 'Вы успешно сохранили префикс'
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}