<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ItemController extends Controller
{
    public function exportItemsCsv()
    {
        $fileName = 'items_export.csv';

        $items = DB::table('items')->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');

            // CSV column headers
            fputcsv($file, array_keys((array) $items->first()));

            // Data rows
            foreach ($items as $item) {
                fputcsv($file, (array) $item);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}