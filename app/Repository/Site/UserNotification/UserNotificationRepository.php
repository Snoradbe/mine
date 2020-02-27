<?php


namespace App\Repository\Site\UserNotification;


use App\Entity\Site\User;
use App\Entity\Site\UserNotification;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserNotificationRepository
{
    /**
     * @param User|null $user
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getAll(?User $user, int $page): LengthAwarePaginator;

    /**
     * @param User $user
     * @return UserNotification[]
     */
    public function getAllByUser(User $user): array;

    /**
     * @param UserNotification $notification
     */
    public function create(UserNotification $notification): void;

    /**
     * @param UserNotification $notification
     */
    public function update(UserNotification $notification): void;

    /**
     * @param User $user
     */
    public function markReadAllForUser(User $user): void;
}