<?php


namespace App\Http\Controllers\Client;


use App\Entity\Site\UserNotification;
use App\Http\Controllers\Controller;
use App\Repository\Site\UserNotification\UserNotificationRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Utils;

class NotificationsController extends Controller
{
    public function loadNotifications(UserNotificationRepository $userNotificationRepository)
    {
        $page = abs((int) request('page', 1));

        $notifications = $userNotificationRepository->getAll(Auth::getUser(), $page);

        return new JsonResponse([
            'notifications' => array_map(function (UserNotification $userNotification) {
                return $userNotification->toArray();
            }, $notifications->all()),
            'pagination' => Utils::paginationData($notifications)
        ]);
    }

    public function readNotifications(UserNotificationRepository $userNotificationRepository)
    {
        $userNotificationRepository->markReadAllForUser(Auth::getUser());
    }

    public function loadLastNotifications(UserNotificationRepository $userNotificationRepository)
    {
        return new JsonResponse([
            'notifications' => array_map(function (UserNotification $userNotification) {
                return $userNotification->toArray();
            }, $userNotificationRepository->getAllByUser(Auth::getUser()))
        ]);
    }
}