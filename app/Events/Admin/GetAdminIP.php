<?php


namespace App\Events\Admin;


use App\Entity\Site\User;

trait GetAdminIP
{
    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->getAdmin()->getLastLoginIP();
    }

    /**
     * @return User
     */
    abstract function getAdmin(): User;
}