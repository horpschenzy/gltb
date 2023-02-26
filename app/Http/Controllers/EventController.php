<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();

        return response()->json(['status' => true, 'data' => $events], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'date' => 'required',
                'image' => 'image|max:2048'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $event = new Event();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public/images', $filename);
            $event->image = $filename;
        }

        $event->title = $request->title;
        $event->description = $request->description;
        $event->date = $request->date;
        $event->save();

        return response()->json(['status' => true, 'message' => 'Event saved successfully'], 201);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json(['status' => true, 'data' => $event], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'date' => 'required',
                'image' => 'image|max:2048'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $event = Event::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public/images', $filename);
            
            if ($event->image && $event->image != $filename) {
                Storage::delete('public/images/'.$event->image);
            }
            $event->image = $filename;
        }

        $event->title = $request->title;
        $event->description = $request->description;
        $event->date = $request->date;
        $event->save();

        return response()->json(['status' => true, 'message' => 'Event updated successfully'], 201);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->image) {
            Storage::delete('public/images/'.$event->image);
        }

        $event->delete();

        return response()->json(['status' => true, 'message' => 'Event deleted successfully'], 201);
    }

}
