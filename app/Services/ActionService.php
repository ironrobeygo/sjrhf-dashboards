<?php

namespace App\Services;

use App\Models\Action;

class ActionService
{
    /**
     * Retrieve actions filtered by fundraiser and category.
     *
     * @param string $fundraiser
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActionsByCategory(string $fundraiser, string $category, int $perPage = 15)
    {
        return Action::where('action_solicitor_list', $fundraiser)
                     ->where('action_category', $category)
                     ->paginate($perPage);
    }

    /**
     * Retrieve actions filtered by fundraiser and type.
     *
     * @param string $fundraiser
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActionsByType(string $fundraiser, string $type, int $perPage = 15)
    {
        return Action::where('action_solicitor_list', $fundraiser)
                     ->where('action_type', $type)
                     ->paginate($perPage);
    }
}