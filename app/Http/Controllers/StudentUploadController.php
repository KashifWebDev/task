<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid file. Please upload a CSV file.',
                'details' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return response()->json([
                'error' => 'Could not read file.'
            ], 422);
        }

        try {
            $header = fgetcsv($handle);
            if ($header === false) {
                return response()->json([
                    'error' => 'File appears to be empty.'
                ], 422);
            }

            $header = array_map('trim', array_map('strtolower', $header));
            $requiredColumns = ['school_name', 'student_name'];
            $missingColumns = array_diff($requiredColumns, $header);

            if (!empty($missingColumns)) {
                return response()->json([
                    'error' => 'Missing required columns: ' . implode(', ', $missingColumns),
                    'found_columns' => $header
                ], 422);
            }

            $schoolNameIndex = array_search('school_name', $header);
            $studentNameIndex = array_search('student_name', $header);

            $schoolCache = [];
            $schoolsCreated = 0;
            $studentsCreated = 0;

            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                if (!isset($row[$schoolNameIndex]) || !isset($row[$studentNameIndex])) {
                    continue;
                }

                $schoolName = trim($row[$schoolNameIndex]);
                $studentName = trim($row[$studentNameIndex]);

                if ($schoolName === '' || $studentName === '') {
                    continue;
                }

                if (!isset($schoolCache[$schoolName])) {
                    $school = School::firstOrCreate(['name' => $schoolName]);

                    $schoolCache[$schoolName] = $school->id;

                    if ($school->wasRecentlyCreated) {
                        $schoolsCreated++;
                    }
                }

                $schoolId = $schoolCache[$schoolName];

                try {
                    Student::create([
                        'school_id' => $schoolId,
                        'name' => $studentName,
                    ]);
                    $studentsCreated++;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() !== '23000') {
                        throw $e;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'schools_created' => $schoolsCreated,
                'students_created' => $studentsCreated,
            ], 200);
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'error' => 'Failed to process file.',
                'message' => $e->getMessage()
            ], 500);
        } finally {
            fclose($handle);
        }
    }
}
