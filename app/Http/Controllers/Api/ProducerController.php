<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'delivery_time' => 'nullable|integer|min:1|max:365',
            'minimum_order_value' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Nazwa producenta jest wymagana.',
            'name.max' => 'Nazwa producenta nie może przekraczać 255 znaków.',
            'phone.max' => 'Numer telefonu nie może przekraczać 20 znaków.',
            'email.email' => 'Podaj poprawny adres email.',
            'delivery_time.integer' => 'Czas dostawy musi być liczbą całkowitą.',
            'delivery_time.min' => 'Czas dostawy nie może być mniejszy niż 1 dzień.',
            'delivery_time.max' => 'Czas dostawy nie może przekraczać 365 dni.',
            'minimum_order_value.numeric' => 'Minimalna wartość zamówienia musi być liczbą.',
            'minimum_order_value.min' => 'Minimalna wartość zamówienia nie może być ujemna.',
        ]);



        $producer = Producer::create($validatedData);

        return response()->json([
            'message' => 'Producer created successfully',
            'producer' => $producer
        ], 201);
    }

    public function getProducers(Request $request)
    {
        $defaultSortBy = 'name';
        $defaultSortOrder = 'asc';
        $defaultPerPage = 10;

        $query = Producer::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . strtolower($request->query('name')) . '%');
        }

        $sortBy = $request->query('sort_by', $defaultSortBy);
        $sortOrder = $request->query('sort_order', $defaultSortOrder);
        $query->orderBy($sortBy, $sortOrder);

        $producers = $query->paginate($request->query('per_page', $defaultPerPage));

        return response()->json($producers);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'delivery_time' => 'nullable|integer|min:1|max:365',
            'minimum_order_value' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Nazwa producenta jest wymagana.',
            'name.max' => 'Nazwa producenta nie może przekraczać 255 znaków.',
            'phone.max' => 'Numer telefonu nie może przekraczać 20 znaków.',
            'email.email' => 'Podaj poprawny adres email.',
            'delivery_time.integer' => 'Czas dostawy musi być liczbą całkowitą.',
            'delivery_time.min' => 'Czas dostawy nie może być mniejszy niż 1 dzień.',
            'delivery_time.max' => 'Czas dostawy nie może przekraczać 365 dni.',
            'minimum_order_value.numeric' => 'Minimalna wartość zamówienia musi być liczbą.',
            'minimum_order_value.min' => 'Minimalna wartość zamówienia nie może być ujemna.',
        ]);

        // Znalezienie i aktualizacja producenta
        $producer = Producer::findOrFail($id);
        $producer->update($validatedData);

        return response()->json([
            'message' => 'Producer updated successfully',
            'producer' => $producer
        ], 200);
    }

    // Funkcja do usuwania producenta
    public function destroy($id)
    {
        // Znalezienie producenta po ID
        $producer = Producer::findOrFail($id);

        // Usunięcie producenta
        $producer->delete();

        return response()->json([
            'message' => 'Producer deleted successfully'
        ], 200);
    }
}
