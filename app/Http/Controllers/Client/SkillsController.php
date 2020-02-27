<?php


namespace App\Http\Controllers\Client;


use App\Entity\Site\Skill;
use App\Exceptions\Exception;
use App\Handlers\Client\Skills\SkillUpHandler;
use App\Http\Controllers\Controller;
use App\Repository\Site\Skills\SkillsRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkillsController extends Controller
{
    public function load(SkillsRepository $skillsRepository)
    {
        return new JsonResponse([
            'skills' => array_map(function (Skill $skill) {
                return $skill->toArray();
            }, $skillsRepository->getAll())
        ]);
    }

    public function skillUp(Request $request, SkillUpHandler $handler)
    {
        try {
            $this->validate($request, [
                'skill' => 'required|integer'
            ]);

            $userSkill = $handler->handle(Auth::getUser(), (int) $request->post('skill'));

            return new JsonResponse([
                'msg' => 'Навык был улучшен',
                'user_skill' => $userSkill->toArray()
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse($exception->validator->errors()->first(), 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}