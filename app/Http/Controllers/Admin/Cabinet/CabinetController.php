<?php


namespace App\Http\Controllers\Admin\Cabinet;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Exceptions\Exception;
use App\Handlers\Admin\Cabinet\ChangePrefixHandler;
use App\Handlers\Admin\Cabinet\DeleteSkinCloakHandler;
use App\Handlers\Admin\Cabinet\GiveGroupHandler;
use App\Handlers\Admin\Cabinet\PlayerInfoHandler;
use App\Handlers\Admin\Cabinet\RemoveGroupHandler;
use App\Handlers\Admin\Cabinet\RemovePrefixHandler;
use App\Handlers\Admin\Cabinet\SetValuteHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Cabinet\Prefix\PrefixSuffix;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CabinetController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'cabinet';

        $servers = $serverRepository->getAll();

        return view('admin.cabinet.index', [
            'servers' => $servers
        ]);
    }

    public function player(Request $request, PlayerInfoHandler $handler)
    {
        NavMenu::$active = 'cabinet';

        try {
            $this->validate($request, [
               'name' => 'required|string|min:2',
               'server' => 'required|integer'
            ]);

            /**
             * @var User $user
             * @var Server $server
             * @var Group[] $groups
             * @var PrefixSuffix $prefix
             */
            [$user, $server, $groups, $prefix, $permissions, $skin, $cloak, $defaultSkin]
                = $handler->handle(Auth::getUser(), $request->get('name'), (int) $request->get('server'));

            $userGroups = $user->getGroups()->filter(function (UserGroup $userGroup) use ($server) {
                return $userGroup->getServer() === $server;
            });

            $prefixSettings = [
                'colors' => config('site.colors', []),
                'min' => 0,
                'max' => 15,
                'regex' => CabinetSettings::getPrefixSettings()['regex'] ?? 'A-Z'
            ];

            return view('admin.cabinet.player', [
                'user' => $user,
                'groups' => $groups,
                'server' => $server,
                'userGroups' => $userGroups,
                'prefix' => $prefix->toArray(),
                'permissions' => $permissions,
                'skin' => $skin,
                'defaultSkin' => $defaultSkin,
                'cloak' => $cloak,
                'prefixSettings' => $prefixSettings
            ]);
        } catch (ValidationException $exception) {
            return redirect()->route('admin.cabinet')->withErrors($exception->errors());
        } catch (Exception $exception) {
            return redirect()->route('admin.cabinet')->withErrors($exception->getMessage());
        }
    }

    public function deleteSkinCloak(Request $request, DeleteSkinCloakHandler $handler, int $userId)
    {
        try {
            $this->validate($request, [
                'type' => 'required|string|in:skin,cloak'
            ]);

            $type = $request->post('type');

            $handler->handle(Auth::getUser(), $userId, $type);

            return redirect()->back()->with('success_message', 'Вы успешно удалили ' . ($type == 'skin' ? 'скин' : 'плащ'));
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function setValute(Request $request, SetValuteHandler $handler, int $userId)
    {
        try {
            $this->validate($request, [
                'type' => 'required|string|in:money,coins',
                'amount' => 'required|integer|min:0|max:99999',
            ]);

            $type = $request->post('type');

            $handler->handle(Auth::getUser(), $userId, $type, (int) $request->post('amount'));

            return redirect()->back()->with('success_message', 'Вы успешно изменили баланс ' . ($type == 'money' ? 'рублей' : 'монет'));
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function giveGroup(Request $request, GiveGroupHandler $handler, int $userId)
    {
        try {
            $this->validate($request, [
                'server' => 'required|integer',
                'group' => 'required|integer',
                'date' => 'nullable|date',
            ]);

            $handler->handle(
                Auth::getUser(),
                $userId,
                (int) $request->post('server'),
                (int) $request->post('group'),
                $request->post('date')
            );

            return redirect()->back()->with('success_message', 'Вы успешно выдали группу');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function removeGroup(Request $request, RemoveGroupHandler $handler, int $userId)
    {
        try {
            $this->validate($request, [
                'user_group' => 'required|integer'
            ]);

            $handler->handle(
                Auth::getUser(),
                $userId,
                (int) $request->post('user_group')
            );

            return redirect()->back()->with('success_message', 'Вы успешно удалили группу');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function changePrefix(Request $request, ChangePrefixHandler $handler, int $userId)
    {
        try {
            $config = [
                'colors' => config('site.colors', []),
                'min' => 0,
                'max' => 15,
                'regex' => CabinetSettings::getPrefixSettings()['regex'] ?? 'A-Z'
            ];
            $colors = implode(',', array_keys($config['colors']));

            $this->validate($request, [
                'server' => 'required|integer',
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
                $userId,
                (int) $request->post('server')
            );

            return redirect()->back()->with('success_message', 'Вы успешно изменили префикс');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function removePrefix(Request $request, RemovePrefixHandler $handler, int $userId)
    {
        try {
            $this->validate($request, [
                'server' => 'required|integer'
            ]);

            $handler->handle(
                Auth::getUser(),
                $userId,
                (int) $request->post('server')
            );

            return redirect()->back()->with('success_message', 'Вы успешно удалили префикс');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}