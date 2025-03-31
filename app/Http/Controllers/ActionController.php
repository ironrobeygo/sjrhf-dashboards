<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Action;

class ActionController extends Controller
{
    public function showFundraiserActionDetails($fundraiser, $category)
    {
        // Fetch actions based on the fundraiser and category
        $actions = Action::where('action_solicitor_list', $fundraiser)
            ->where('action_category', $category)
            ->get();

        return view('actions.summary', [
            'actions' => $actions,
            'fundraiser' => $fundraiser,
            'category' => $category
        ]);
    }

    public function showFundraiserActionType($fundraiser, $type){
        // Fetch actions based on the fundraiser and category
        $actions = Action::where('action_solicitor_list', $fundraiser)
            ->where('action_type', $type)
            ->get();

        return view('actions.type', [
            'actions' => $actions,
            'fundraiser' => $fundraiser,
            'type' => $type
        ]);
    }
}
