<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AppointmentController extends Controller
{
    public function index(): JsonResponse
    {
        $appointments = Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(request('status'), fn (Builder $query) => $query->where('status', AppointmentStatus::from(request('status'))))
            ->latest()
            ->paginate()
            ->through(fn (Appointment $appointment) => [
                'id' => $appointment->id,
                'start_time' => $appointment->start_time->format('Y-m-d h:i A'),
                'end_time' => $appointment->end_time->format('Y-m-d h:i A'),
                'status' => [
                    'name' => $appointment->status->name,
                    'color' => $appointment->status->color(),
                ],
                'client' => $appointment->client,
            ]);

        return response()->json($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        Appointment::create([
            'title' => $request->input('title'),
            'client_id' => 1,
            'start_time' => now(),
            'end_time' => now(),
            'description' => $request->input('description'),
            'status' => AppointmentStatus::SCHEDULED,
        ]);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    public function getStatusWithCount(): Collection
    {
        return collect(AppointmentStatus::cases())->map(
            fn (AppointmentStatus $status) => [
                'name' => $status->name,
                'value' => $status->value,
                'count' => Appointment::where('status', $status->value)->count(),
                'color' => AppointmentStatus::from($status->value)->color(),
            ]
        );
    }
}
