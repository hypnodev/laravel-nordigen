<?php

namespace Hypnodev\LaravelNordigen\Http\Controllers;

use App\Models\User;
use Hypnodev\LaravelNordigen\Facades\LaravelNordigen;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Nordigen\NordigenPHP\Enums\RequisitionStatus;

class AuthorizationController extends Controller
{
    use ValidatesRequests;

    /**
     * Store Nordigen requisition ref to database, so we can take it after and get whatever we want
     *
     * @param Request $request
     *
     * @return Redirector|RedirectResponse
     */
    public function store(Request $request): Redirector|RedirectResponse
    {
        try {
            $this->validate($request, [
                'ref' => ['required', 'string', 'uuid'],
                'user_id' => ['required', 'numeric', 'exists:users,id']
            ]);
        } catch (ValidationException) {
            abort(422);
        }

        $ref = $request->query('ref');
        $requisition = LaravelNordigen::nordigenClient()->requisition->getRequisition($ref);
        if ($requisition['status'] !== RequisitionStatus::LINKED) {
            $fallbackUri = config('laravel-nordigen.redirect.fallback_uri');
            $redirectUrl = url(
                "$fallbackUri?" . http_build_query(['status' => $requisition['status']])
            );

            return redirect($redirectUrl);
        }

        $userId = $request->query('user_id');
        $user = User::find($userId);
        $user->nordigenRequisition()->firstOrCreate(
            [ 'reference' => $requisition['reference'] ],
            [ 'institution_id' => $requisition['institution_id'], 'agreement' => $requisition['reference'] ]
        );

        $successUri = config('laravel-nordigen.redirect.success_uri');
        $redirectUrl = url($successUri);
        return redirect($redirectUrl);
    }
}
