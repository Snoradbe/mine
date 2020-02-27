<?php


namespace App\Http\Requests\Admin\Discounts;


use App\Repository\Site\Group\GroupRepository;
use Illuminate\Foundation\Http\FormRequest;

class AddDiscountRequest extends FormRequest
{
    public function rules(GroupRepository $groupRepository): array
    {
        $groups = $groupRepository->getAllDonate();

        $modules = [];
        foreach (config('site.discount.modules', []) as $module => $name)
        {
            $modules[] = 'module_' . $module;
        }

        $types = array_merge($modules, [
            'all',
            'groups_all',
            'groups_primary',
            'groups_other',
        ]);

        foreach ($groups as $group)
        {
            $types[] = 'group_' . $group->getName();
        }

        return [
            'server' => 'nullable|integer',
            'type' => 'required|in:' . implode(',', $types),
            'discount' => 'required|integer|min:1|max:99',
            'date' => 'required|string'
        ];
    }
}