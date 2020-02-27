<?php


namespace App\Services\Auth;


interface AuthService
{
    public function check(): bool;
}