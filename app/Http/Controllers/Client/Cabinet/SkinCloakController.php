<?php


namespace App\Http\Controllers\Client\Cabinet;


use App\Exceptions\Exception;
use App\Handlers\Client\Cabinet\DeleteCloakHandler;
use App\Handlers\Client\Cabinet\DeleteSkinHandler;
use App\Handlers\Client\Cabinet\UploadCloakHandler;
use App\Handlers\Client\Cabinet\UploadSkinHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkinCloakController extends Controller
{
    public function uploadSkin(Request $request, UploadSkinHandler $handler)
    {
        try {
            $this->validate($request, [
                'file' => 'required|file|image|mimetypes:image/png|max:' . CabinetSettings::getSkinCloakSize('skin')
            ]);

            $handler->handle(Auth::getUser(), $request->file('file'));

            return new JsonResponse(['msg' => 'Вы успешно загрузили скин']);

        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function deleteSkin(DeleteSkinHandler $handler)
    {
        try {
            $handler->handle(Auth::getUser());

            return new JsonResponse(['msg' => 'Вы успешно удалили скин']);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function uploadCloak(Request $request, UploadCloakHandler $handler)
    {
        try {
            $this->validate($request, [
                'file' => 'required|file|image|mimetypes:image/png|max:' . CabinetSettings::getSkinCloakSize('cloak')
            ]);

            $handler->handle(Auth::getUser(), $request->file('file'));

            return new JsonResponse(['msg' => 'Вы успешно загрузили плаш']);

        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function deleteCloak(DeleteCloakHandler $handler)
    {
        try {
            $handler->handle(Auth::getUser());

            return new JsonResponse(['msg' => 'Вы успешно удалили плащ']);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}