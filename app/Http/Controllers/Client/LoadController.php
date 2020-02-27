<?php


namespace App\Http\Controllers\Client;


use App\Entity\Site\Discount;
use App\Entity\Site\Server;
use App\Entity\Site\UserNotification;
use App\Helpers\LevelHelper;
use App\Http\Controllers\Controller;
use App\Repository\Game\LiteBans\LiteBansRepository;
use App\Repository\Site\Discount\DiscountRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\UserNotification\UserNotificationRepository;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Response\JsonResponse;
use App\Services\Settings\DataType;

class LoadController extends Controller
{
    /**
     * Загрузка первоначальных данных
     * Модуль может иметь свойства:
     *    - name: название
     *    - fa: иконка (fa-gavel)
     *    - is_static: статичное положение? (в нижней части)
     *    - need_server: Нужен выбор сервера для этого модуля? (будет отображаться выбранный сервер в верхнем меню)
     *    - data: массив дополнительных данных для модуля
     *    - is_hide: модуль будет скрыт в меню? (например если ссылка на него из другого места)
     *
     * @param ServerRepository $serverRepository
     * @param LiteBansRepository $liteBansRepository
     * @param UserNotificationRepository $userNotificationRepository
     * @param DiscountRepository $discountRepository
     * @return JsonResponse
     */
    public function load(
        ServerRepository $serverRepository,
        LiteBansRepository $liteBansRepository,
        UserNotificationRepository $userNotificationRepository,
        DiscountRepository $discountRepository)
    {
        $user = Auth::getUser();

        $servers = array_map(function (Server $server) {
            return $server->toArray();
        }, $serverRepository->getAll());

        $selectedServer = 0;
        $selectedModule = 'cabinet';

        $ban = $liteBansRepository->findByUser($user);

        $modules = [];

        //Разбан
        if (!is_null($ban)) {
            $modules['unban'] = [
                'name' => 'РАЗБАН',
                'fa' => 'fa-gavel',
                'is_static' => false,
                'need_server' => false,
                'data' => [
                    'ban' => $ban->toArray(),
                    'price' => settings('unban', DataType::INT, 9999)
                ]
            ];
            $selectedModule = 'unban';
        }

        //Кабинет
        $modules['cabinet'] = [
            'name' => 'КАБИНЕТ',
            'fa' => 'fa-user',
            'is_static' => false,
            'need_server' => true,
        ];

        //Магазин
        $modules['shop'] = [
            'name' => 'МАГАЗИН',
            'fa' => 'fa-shopping-bag',
            'is_static' => false,
            'need_server' => true,
        ];

        //Заявки
        //if (!$user->inTeam() && is_null($ban)) {
            $applications = settings('applications', DataType::JSON, []);
            $applicationStatuses = $applications['statuses'] ?? [];
            $showApplications = false;
            foreach ($applicationStatuses as $applicationStatus => $applicationData)
            {
                $applicationServers = array_filter($applicationData['enabled'], function ($enabled) {
                    return $enabled;
                });

                if (!empty($applicationServers) && is_array($applicationServers)) {
                    $showApplications = true;
                    break;
                }
            }

            if ($showApplications) {
                $modules['applications'] = [
                    'name' => 'ЗАЯВКИ',
                    'fa' => 'fa-file-alt',
                    'is_static' => false,
                    'need_server' => true,
                    'data' => [
                        'min_level' => $applications['min_level'] ?? 1
                    ]
                ];
            }
        //}

        $modules['notifications'] = [
            'name' => 'Уведомления',
            'is_hide' => true
        ];

        $modules['votes'] = [
            'name' => 'ГОЛОСОВАНИЕ',
            'fa' => 'fa-thumbs-up'
        ];

        $modules['referal'] = [
            'name' => 'РЕФЕРАЛЫ',
            'fa' => 'fa-sitemap'
        ];

        $modules['skills'] = [
            'name' => 'УЛУЧШЕНИЯ',
            'fa' => 'fa-receipt',
            'need_server' => true,
        ];

        $modules = array_merge($modules, [
            'settings' => [
                'name' => 'НАСТРОЙКИ',
                'is_static' => true,
            ],
            'payment' => [
                'name' => 'ПОПОЛНИТЬ СЧЕТ',
                'is_static' => true,
            ],
            'promo' => [
                'name' => 'ПРОМО КОД',
                'is_static' => true,
            ],
            'logs' => [
                'name' => 'ИСТОРИЯ ОПЕРАЦИЙ',
                'is_static' => true,
            ],
            'bugreport' => [
                'name' => 'БАГ-РЕПОРТ',
                'is_static' => true,
                'need_server' => true
            ],
            'pay5' => [
                'name' => '---',
                'is_static' => true,
            ],
        ]);

        /*$userNotificationRepository->create(new UserNotify(
            $user,
            'Первое уведомление.<br>Ура!!!<br><br><i class="fa fa-blender"></i>'
        ));*/

        return new JsonResponse([
            'modules' => $modules,
            'module' => $selectedModule,
            'user' => $user->toArray(true),
            'servers' => $servers,
            'server' => $selectedServer,
            'notifications' => array_map(function (UserNotification $userNotification) {
                return $userNotification->toArray();
            }, $userNotificationRepository->getAllByUser($user)),
            'discounts' => array_map(function (Discount $discount) {
                return $discount->toArray();
            }, $discountRepository->getAll()),
            'default_permissions' => CabinetSettings::getDefaultPermissions(),
            'level_settings' => [
                'base' => LevelHelper::BASE,
                'step' => LevelHelper::STEP,
                'factor' => LevelHelper::FACTOR,
                'factor_exp' => LevelHelper::FACTOR_EXP,
            ]
        ]);
    }
}