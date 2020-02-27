<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        /* ЗАЯВКИ */
        \App\Events\Admin\Applications\ManageEvent::class => [
            \App\Listeners\Admin\Applications\ManageListener::class
        ],
        \App\Events\Admin\Applications\Settings\AddGroupEvent::class => [
            \App\Listeners\Admin\Applications\Settings\AddGroupListener::class
        ],
        \App\Events\Admin\Applications\Settings\DeleteGroupEvent::class => [
            \App\Listeners\Admin\Applications\Settings\DeleteGroupListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditDescriptionEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditDescriptionListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditFormEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditFormListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditGroupEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditGroupListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditRulesEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditRulesListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditServerFormEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditServerFormListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditCooldownEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditCooldownListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditMinLevelEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditMinLevelListener::class
        ],
        \App\Events\Admin\Applications\Settings\EditServerSelfFormEvent::class => [
            \App\Listeners\Admin\Applications\Settings\EditServerSelfFormListener::class
        ],

        /* БАНЫ */
        \App\Events\Admin\Banlist\BanEvent::class => [
            \App\Listeners\Admin\Banlist\BanListener::class
        ],
        \App\Events\Admin\Banlist\UnbanEvent::class => [
            \App\Listeners\Admin\Banlist\UnbanListener::class
        ],

        /* НАСТРОЙКИ */
        \App\Events\Admin\Cabinet\Settings\GameMoney\GameMoneySettingsEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\GameMoney\GameMoneySettingsListener::class
        ],
        \App\Events\Admin\Cabinet\Settings\Groups\AddGroupEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\Groups\AddGroupListener::class
        ],
        \App\Events\Admin\Cabinet\Settings\Groups\AddPeriodEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\Groups\AddPeriodListener::class
        ],
        \App\Events\Admin\Cabinet\Settings\Groups\RemoveGroupEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\Groups\RemoveGroupListener::class
        ],
        \App\Events\Admin\Cabinet\Settings\Groups\RemovePeriodEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\Groups\RemovePeriodListener::class
        ],
        \App\Events\Admin\Cabinet\Settings\Prefix\PrefixSettingsEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\Prefix\PrefixSettingsListener::class
        ],
        \App\Events\Admin\Cabinet\Settings\SkinCloakSettingsEvent::class => [
            \App\Listeners\Admin\Cabinet\Settings\SkinCloakSettingsListener::class
        ],
        \App\Events\Admin\Tops\TopsSettingsEvent::class => [
            \App\Listeners\Admin\Tops\TopsSettingsListener::class
        ],
        \App\Events\Admin\Unban\UnbanSettingsEvent::class => [
            \App\Listeners\Admin\Unban\UnbanSettingsListener::class
        ],
        \App\Events\Admin\Referal\ReferalSettingsEvent::class => [
            \App\Listeners\Admin\Referal\ReferalSettingsListener::class
        ],

        /* ГРУППЫ */
        \App\Events\Admin\Groups\AddGroupEvent::class => [
            \App\Listeners\Admin\Groups\AddGroupListener::class
        ],
        \App\Events\Admin\Groups\EditGroupEvent::class => [
            \App\Listeners\Admin\Groups\EditGroupListener::class
        ],
        \App\Events\Admin\Groups\DeleteGroupEvent::class => [
            \App\Listeners\Admin\Groups\DeleteGroupListener::class
        ],

        /* ПЕРМИШЕНЫ */
        \App\Events\Admin\ServerPermissions\AddPermissionEvent::class => [
            \App\Listeners\Admin\ServerPermissions\AddPermissionListener::class
        ],
        \App\Events\Admin\ServerPermissions\DeletePermissionEvent::class => [
            \App\Listeners\Admin\ServerPermissions\DeletePermissionListener::class
        ],

        /* СЕРВЕРЫ */
        \App\Events\Admin\Servers\AddServerEvent::class => [
            \App\Listeners\Admin\Servers\AddServerListener::class
        ],
        \App\Events\Admin\Servers\EditServerEvent::class => [
            \App\Listeners\Admin\Servers\EditServerListener::class
        ],
        \App\Events\Admin\Servers\BeforeDeleteServerEvent::class => [
            \App\Listeners\Admin\Servers\BeforeDeleteServerListener::class
        ],
        \App\Events\Admin\Servers\DeleteServerEvent::class => [
            \App\Listeners\Admin\Servers\DeleteServerListener::class
        ],

        /* АДМИНИСТРАЦИЯ */
        \App\Events\Admin\Team\AddToTeamEvent::class => [
            \App\Listeners\Admin\Team\AddToTeamListener::class
        ],
        \App\Events\Admin\Team\TransitTeamEvent::class => [
            \App\Listeners\Admin\Team\TransitTeamListener::class
        ],
        \App\Events\Admin\Team\UpdateTeamEvent::class => [
            \App\Listeners\Admin\Team\UpdateTeamListener::class
        ],
        \App\Events\Admin\Team\DeleteFromTeamEvent::class => [
            \App\Listeners\Admin\Team\DeleteFromTeamListener::class
        ],

        /* ВАУЧЕРЫ */
        \App\Events\Admin\Vauchers\AddVaucherEvent::class => [
            \App\Listeners\Admin\Vauchers\AddVaucherListener::class
        ],
        \App\Events\Admin\Vauchers\EditVaucherEvent::class => [
            \App\Listeners\Admin\Vauchers\EditVaucherListener::class
        ],
        \App\Events\Admin\Vauchers\DeleteVaucherEvent::class => [
            \App\Listeners\Admin\Vauchers\DeleteVaucherListener::class
        ],

        /* ПРАВА В АДМИН-ПАНЕЛИ */
        \App\Events\Admin\AdminPerms\AddPermissionsEvent::class => [
            \App\Listeners\Admin\AdminPerms\AddPermissionsListener::class
        ],
        \App\Events\Admin\AdminPerms\DeletePermissionsEvent::class => [
            \App\Listeners\Admin\AdminPerms\DeletePermissionsListener::class
        ],

        /* ПРАВА НА САЙТЕ */
        \App\Events\Admin\SitePerms\AddPermissionsEvent::class => [
            \App\Listeners\Admin\SitePerms\AddPermissionsListener::class
        ],
        \App\Events\Admin\SitePerms\DeletePermissionsEvent::class => [
            \App\Listeners\Admin\SitePerms\DeletePermissionsListener::class
        ],

        /* МАГАЗИН */
        \App\Events\Admin\Shop\Category\AddCategoryEvent::class => [
            \App\Listeners\Admin\Shop\Category\AddCategoryListener::class
        ],
        \App\Events\Admin\Shop\Category\EditCategoryEvent::class => [
            \App\Listeners\Admin\Shop\Category\EditCategoryListener::class
        ],
        \App\Events\Admin\Shop\Item\AddItemEvent::class => [
            \App\Listeners\Admin\Shop\Item\AddItemListener::class
        ],
        \App\Events\Admin\Shop\Item\EditItemEvent::class => [
            \App\Listeners\Admin\Shop\Item\EditItemListener::class
        ],
        \App\Events\Admin\Shop\Player\GiveProductEvent::class => [
            \App\Listeners\Admin\Shop\Player\GiveProductListener::class
        ],
        \App\Events\Admin\Shop\Player\RemovePurchaseEvent::class => [
            \App\Listeners\Admin\Shop\Player\RemovePurchaseListener::class
        ],
        \App\Events\Admin\Shop\Product\AddProductEvent::class => [
            \App\Listeners\Admin\Shop\Product\AddProductListener::class
        ],
        \App\Events\Admin\Shop\Product\EditProductEvent::class => [
            \App\Listeners\Admin\Shop\Product\EditProductListener::class
        ],
        \App\Events\Admin\Shop\Product\DeleteProductEvent::class => [
            \App\Listeners\Admin\Shop\Product\DeleteProductListener::class
        ],
        \App\Events\Admin\Shop\Product\Discount\SetDiscountEvent::class => [
            \App\Listeners\Admin\Shop\Product\Discount\SetDiscountListener::class
        ],
        \App\Events\Admin\Shop\Product\Discount\RandomDiscountEvent::class => [
            \App\Listeners\Admin\Shop\Product\Discount\RandomDiscountListener::class
        ],

        //СКИДКИ
        \App\Events\Admin\Discounts\AddDiscountEvent::class => [
            \App\Listeners\Admin\Discounts\AddDiscountListener::class
        ],
        \App\Events\Admin\Discounts\DeleteDiscountEvent::class => [
            \App\Listeners\Admin\Discounts\DeleteDiscountListener::class
        ],

        //БАГ-РЕПОРТЫ
        \App\Events\Admin\BugReport\ChangeStatusEvent::class => [
            \App\Listeners\Admin\BugReport\ChangeStatusListener::class
        ],
        \App\Events\Admin\BugReport\SendMessageEvent::class => [
            \App\Listeners\Admin\BugReport\SendMessageListener::class
        ],

        //КАБИНЕТ
        \App\Events\Admin\Cabinet\ChangePrefixEvent::class => [
            \App\Listeners\Admin\Cabinet\ChangePrefixListener::class
        ],
        \App\Events\Admin\Cabinet\DeleteSkinCloakEvent::class => [
            \App\Listeners\Admin\Cabinet\DeleteSkinCloakListener::class
        ],
        \App\Events\Admin\Cabinet\GiveGroupEvent::class => [
            \App\Listeners\Admin\Cabinet\GiveGroupListener::class
        ],
        \App\Events\Admin\Cabinet\RemoveGroupEvent::class => [
            \App\Listeners\Admin\Cabinet\RemoveGroupListener::class
        ],
        \App\Events\Admin\Cabinet\RemovePrefixEvent::class => [
            \App\Listeners\Admin\Cabinet\RemovePrefixListener::class
        ],
        \App\Events\Admin\Cabinet\SetValuteEvent::class => [
            \App\Listeners\Admin\Cabinet\SetValuteListener::class
        ],

        //HWID БАНЫ
        \App\Events\Admin\HwidBans\HwidBanEvent::class => [
            \App\Listeners\Admin\HwidBans\HwidBanListener::class
        ],
        \App\Events\Admin\HwidBans\HwidUnbanEvent::class => [
            \App\Listeners\Admin\HwidBans\HwidUnbanListener::class
        ],



        /* КЛИЕНТ */
        \App\Events\Client\Cabinet\BuyCasesEvent::class => [
            \App\Listeners\Client\Cabinet\BuyCasesListener::class,
        ],
        \App\Events\Client\Cabinet\BuyGroupEvent::class => [
            \App\Listeners\Client\Cabinet\BuyGroupListener::class,
        ],
        \App\Events\Client\Cabinet\ChangePrefixEvent::class => [
            \App\Listeners\Client\Cabinet\ChangePrefixListener::class,
        ],
        \App\Events\Client\Cabinet\SkinCloakDeleteEvent::class => [
            \App\Listeners\Client\Cabinet\SkinCloakDeleteListener::class,
        ],
        \App\Events\Client\Cabinet\SkinCloakUploadEvent::class => [
            \App\Listeners\Client\Cabinet\SkinCloakUploadListener::class,
        ],
        \App\Events\Client\Cabinet\UnbanEvent::class => [
            \App\Listeners\Client\Cabinet\UnbanListener::class,
        ],
        \App\Events\Client\Cabinet\GameMoneyEvent::class => [
            \App\Listeners\Client\Cabinet\GameMoneyListener::class,
        ],

        \App\Events\Client\Applications\SendApplicationEvent::class => [
            \App\Listeners\Client\Applications\SendApplicationListener::class,
        ],

        \App\Events\Client\Payment\OrderEvent::class => [
            \App\Listeners\Client\Payment\OrderListener::class,
        ],

        \App\Events\Client\Shop\BuyProductEvent::class => [
            \App\Listeners\Client\Shop\BuyProductListener::class,
        ],
        \App\Events\Client\Shop\CancelPurchaseEvent::class => [
            \App\Listeners\Client\Shop\CancelPurchaseListener::class,
        ],

        \App\Events\Client\Promo\UsePromoEvent::class => [
            \App\Listeners\Client\Promo\UsePromoListener::class,
        ],

        \App\Events\Client\Unban\UnbanEvent::class => [
            \App\Listeners\Client\Unban\UnbanListener::class,
        ],

        \App\Events\Client\BugReport\SendReportEvent::class => [
            \App\Listeners\Client\BugReport\SendReportListener::class,
        ],
        \App\Events\Client\BugReport\SendMessageEvent::class => [
            \App\Listeners\Client\BugReport\SendMessageListener::class,
        ],

        \App\Events\Client\Skills\SkillUpEvent::class => [
            \App\Listeners\Client\Skills\SkillUpListener::class,
        ],

        \App\Events\Client\Settings\Google2faEvent::class => [
            \App\Listeners\Client\Settings\Google2faListener::class,
        ],




        /* API */
        \App\Events\Api\User\LevelUpEvent::class => [
            \App\Listeners\Api\User\LevelUpListener::class,
        ],
        \App\Events\Api\User\PaymentEvent::class => [
            \App\Listeners\Api\User\PaymentListener::class,
        ],
        \App\Events\Api\Vote\VoteEvent::class => [
            \App\Listeners\Api\Vote\VoteListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
