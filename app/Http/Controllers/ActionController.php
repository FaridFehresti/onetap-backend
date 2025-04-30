<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActionController extends Controller
{
    public function index()
    {
        $actions = Action::with(['images', 'socialLinks', 'workExperiences', 'educations', 'awards'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $actions,
        ]);
    }

    public function show(Action $action)
    {
        $action->load(['images', 'socialLinks', 'workExperiences', 'educations', 'awards']);

        return response()->json([
            'status' => 'success',
            'data' => $action,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAction($request);

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '_avatar_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('actions'), $avatarName);
            $data['avatar'] = 'actions/' . $avatarName;
        }

        $action = Action::create($data);

        $this->syncRelations($request, $action);

        return response()->json([
            'status' => 'success',
            'data' => $action->load(['images', 'socialLinks', 'workExperiences', 'educations', 'awards']),
        ], 201);
    }

    public function update(Request $request, Action $action)
    {
        $data = $this->validateAction($request);

        if ($request->hasFile('avatar')) {
            if ($action->avatar && file_exists(public_path($action->avatar))) {
                unlink(public_path($action->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '_avatar_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('actions'), $avatarName);
            $data['avatar'] = 'actions/' . $avatarName;
        }

        $action->update($data);

        $this->syncRelations($request, $action);

        return response()->json([
            'status' => 'success',
            'message' => 'Action updated successfully.',
            'data' => $action->load(['images', 'socialLinks', 'workExperiences', 'educations', 'awards']),
        ]);
    }

    public function destroy(Action $action)
    {
        if ($action->avatar && file_exists(public_path($action->avatar))) {
            unlink(public_path($action->avatar));
        }

        $action->images()->delete();
        $action->socialLinks()->delete();
        $action->workExperiences()->delete();
        $action->educations()->delete();
        $action->awards()->delete();

        $action->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Action deleted successfully.',
        ]);
    }

    protected function validateAction(Request $request): array
    {
        $rules = [
            'title' => 'required|string',
            'action_type' => 'required|in:portfolio,contract,crm,redirect,booking',
            'status' => 'required|in:active,inactive',
            'link' => 'required|string',
            'card_id' => 'required|exists:my_cards,id',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'tertiary_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'description' => 'nullable|string',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'position' => 'nullable|string',
            'person_title' => 'nullable|string',
            'contact_link' => 'nullable|string',
            'maximum_participants' => 'nullable|integer|min:1',
            'minimum_participants' => 'nullable|integer|min:1',
            'duration' => 'nullable|integer',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'price' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'booking_link' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        switch ($request->input('action_type')) {
            case 'portfolio':
                $rules['header_text'] = 'required|string';
                break;
            case 'contract':
                $rules['company_name'] = 'required|string';
                $rules['email'] = 'required|string';
                break;
            case 'crm':
                $rules['first_name'] = 'required|string';
                $rules['last_name'] = 'required|string';
                $rules['email'] = 'required|string';
                $rules['phone_number'] = 'required|string';
                break;
            case 'redirect':
                $rules['link'] = 'required|string';
                break;
            case 'booking':
                $rules['maximum_participants'] = 'required|integer|min:1';
                $rules['minimum_participants'] = 'required|integer|min:1';
                $rules['duration'] = 'required|integer';
                $rules['start_time'] = 'required|date_format:H:i';
                $rules['end_time'] = 'required|date_format:H:i';
                $rules['price'] = 'required|numeric';
                $rules['currency'] = 'required|string';
                $rules['booking_link'] = 'required|string';
                break;
        }

        return $request->validate($rules);
    }

    protected function syncRelations(Request $request, Action $action)
    {
        if ($request->has('images')) {
            $action->images()->delete();
            foreach ($request->input('images') as $img) {
                $action->images()->create(['url' => $img]);
            }
        }

        if ($request->has('social_links')) {
            $action->socialLinks()->delete();
            foreach ($request->input('social_links') as $link) {
                $action->socialLinks()->create($link);
            }
        }

        if ($request->has('work_experiences')) {
            $action->workExperiences()->delete();
            foreach ($request->input('work_experiences') as $item) {
                $action->workExperiences()->create($item);
            }
        }

        if ($request->has('educations')) {
            $action->educations()->delete();
            foreach ($request->input('educations') as $edu) {
                $action->educations()->create($edu);
            }
        }

        if ($request->has('awards')) {
            $action->awards()->delete();
            foreach ($request->input('awards') as $award) {
                $action->awards()->create($award);
            }
        }
    }
}
