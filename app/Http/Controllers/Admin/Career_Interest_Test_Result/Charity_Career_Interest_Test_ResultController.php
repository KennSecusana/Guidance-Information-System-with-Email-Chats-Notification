<?php

namespace App\Http\Controllers\Admin\Career_Interest_Test_Result;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Anecdotal_Record;
use App\Models\Counseling_Anecdotal_Record;
use App\Models\Career_Interest_Test_Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Rules\MatchOldPassword;
use Response;
use DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParentExport;
use Image;
use Illuminate\Support\Facades\Storage;

class Charity_Career_Interest_Test_ResultController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id){
 
      $career_interest_test_result_charity = Career_Interest_Test_Result::with(['student'])->where('student_id', '=', $id)->get();
      

      $student_cha = Student::find($id);
      return view('admin.student.Charity.Career_Interest_Test_Result.index', compact('career_interest_test_result_charity', 'student_cha'));
    }

    public function store(Request $request){

        $result = new Career_Interest_Test_Result;
        $result->student_id = $request->input('student_id');

        if($request->hasFile('interest_result')){
            $file = $request->file('interest_result');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            Image::make($file)->save(storage_path('/app/public/career_interest_test_result/' . $filename));

            $result->interest_result= $filename;
            $result->save();

        }
        return redirect()->back()->with('status', 'Record uploaded successfully!');

    }

    public function destroy($id){
        $removeRec = Career_Interest_Test_Result::findOrFail($id);
        $destination = 'storage/career_interest_test_result/'.$removeRec->interest_result;
        if(File::exists($destination)){
            File::delete($destination);
        }
        $removeRec -> delete();
        return redirect()->back()->with('status', 'Record Deleted Successfully!');   
      }


      public function downloadFile($id)
    {
        $charityStudents_career_interest_test_result = Career_Interest_Test_Result::findOrFail($id);
        $pdf = PDF::loadVIew('pdf.charity-career_interest_test_result', [
            'career_interest_test_results' => $charityStudents_career_interest_test_result
        ]);

        return $pdf->download('Charity-Career Interest Test Result.pdf');
    }
}