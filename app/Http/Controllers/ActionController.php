<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActionService;

class ActionController extends Controller
{
    protected $actionService;

    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    public function showFundraiserActionDetails($fundraiser, $category)
    {
        $actions = $this->actionService->getActionsByCategory($fundraiser, $category);
        return view('actions.summary', [
            'actions'    => $actions,
            'fundraiser' => $fundraiser,
            'category'   => $category
        ]);
    }

    public function showFundraiserActionType($fundraiser, $type)
    {
        $actions = $this->actionService->getActionsByType($fundraiser, $type);
        return view('actions.type', [
            'actions'    => $actions,
            'fundraiser' => $fundraiser,
            'type'       => $type
        ]);
    }
}