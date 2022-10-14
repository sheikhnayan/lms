<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageGenerateFromHTML;
use App\Traits\ImageSaveTrait;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use PDF;
use Auth;


class CertificateController extends Controller
{
    use General, ImageSaveTrait, ImageGenerateFromHTML;

    public function index()
    {
        if (!Auth::user()->can('manage_certificate')) {
            abort('403');
        } // end permission checking

        $data['title'] = ' Certificates';
        $data['navCertificateActiveClass'] = "mm-active";
        $data['subNavAllCertificateActiveClass'] = "mm-active";
        $data['certificates'] = Certificate::paginate(25);
        return view('admin.certificate.index')->with($data);
    }


    public function create()
    {
        if (!Auth::user()->can('manage_certificate')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Add Certificate';
        $data['navCertificateActiveClass'] = "mm-active";
        $data['subNavAddCertificateActiveClass'] = "mm-active";
        return view('admin.certificate.create')->with($data);
    }


    public function store(Request $request)
    {
        if (!Auth::user()->can('manage_certificate')) {
            abort('403');
        } // end permission checking

        $request->validate([
            'show_number' => 'required',
            'number_y_position' => 'required',
            'number_font_size' => 'required',
            'title' => 'required',
            'title_y_position' => 'required',
            'title_font_size' => 'required',
            'show_date' => 'required',
            'date_y_position' => 'required',
            'date_font_size' => 'required',
            'show_student_name' => 'required',
            'student_name_y_position' => 'required',
            'student_name_font_size' => 'required',
            'body' => 'required',
            'body_y_position' => 'required',
            'body_font_size' => 'required',
            'role_1_title' => 'required',
            'role_1_y_position' => 'required',
            'role_1_font_size' => 'required',
            'role_2_title' => 'required',
            'role_2_y_position' => 'required',
            'role_2_font_size' => 'required',
            'background_image' => 'required|mimes:jpg,png|file|dimensions:min_width=1030,min_height=734,max_width=1030,max_height=734',
            'role_1_signature' => 'required|mimes:png|file|dimensions:min_width=120,min_height=60,max_width=120,max_height=60',
        ]);

        $certificate = new Certificate();
        $certificate->fill($request->all());
        $certificate->certificate_number = rand(1000000, 9999999);
        $certificate->image = $request->background_image ? $this->saveImage('certificate', $request->background_image, null, null) :   null;
        $certificate->role_1_signature = $request->role_1_signature ? $this->saveImage('certificate', $request->role_1_signature, null, null) :   null;
        $certificate->save();


        /** === make pdf certificate ===== */
        $certificate_name = 'certificate-' . $certificate->uuid. '.pdf';
        // make sure email invoice is checked.
        $customPaper = array(0, 0, 612, 500);
        $pdf = PDF::loadView('admin.certificate.pdf', ['certificate' => $certificate])->setPaper($customPaper, 'portrait');;
        //return $pdf->stream($certificate_name);
        $pdf->save(public_path() . '/uploads/certificate/' . $certificate_name);
        /** === make pdf certificate ===== */
        $certificate->path =  "/uploads/certificate/$certificate_name";
        $certificate->save();

        $this->showToastrMessage('success', 'Certificate has been created');
        return redirect(route('certificate.edit', [$certificate->uuid]));
    }

    public function edit($uuid)
    {
        if (!Auth::user()->can('manage_certificate')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'View Certificate';
        $data['navCertificateActiveClass'] = "mm-active";
        $data['subNavAddCertificateActiveClass'] = "mm-active";
        $data['certificate'] = Certificate::whereUuid($uuid)->first();
        return view('admin.certificate.edit')->with($data);
    }

    public function update(Request $request, $uuid)
    {
        if (!Auth::user()->can('manage_certificate')) {
            abort('403');
        } // end permission checking

        $request->validate([
            'show_number' => 'required',
            'number_y_position' => 'required',
            'number_font_size' => 'required',
            'title' => 'required',
            'title_y_position' => 'required',
            'title_font_size' => 'required',
            'show_date' => 'required',
            'date_y_position' => 'required',
            'date_font_size' => 'required',
            'show_student_name' => 'required',
            'student_name_y_position' => 'required',
            'student_name_font_size' => 'required',
            'body' => 'required',
            'body_y_position' => 'required',
            'body_font_size' => 'required',
            'role_1_title' => 'required',
            'role_1_y_position' => 'required',
            'role_1_font_size' => 'required',
            'role_2_title' => 'required',
            'role_2_y_position' => 'required',
            'role_2_font_size' => 'required',
            'background_image' => 'mimes:jpg,png|file|dimensions:min_width=1030,min_height=734,max_width=1030,max_height=734',
            'role_1_signature' => 'mimes:png|file|dimensions:min_width=120,min_height=60,max_width=120,max_height=60',
        ]);

        $certificate = Certificate::whereUuid($uuid)->first();
        $certificate->fill($request->all());
        if ($request->background_image)
        {
            $this->deleteFile($certificate->image);
            $certificate->image = $this->saveImage('certificate', $request->background_image, '1030', '730');
        }

        if ($request->role_1_signature)
        {
            $this->deleteFile($certificate->role_1_signature);
            $certificate->role_1_signature = $this->saveImage('certificate', $request->role_1_signature, '120', '60');
        }

        $this->deleteFile( $certificate->path);

        /** === make pdf certificate ===== */
        $certificate_name = 'certificate-' . $certificate->uuid. '.pdf';
        // make sure email invoice is checked.
        $customPaper = array(0, 0, 612, 500);
        $pdf = PDF::loadView('admin.certificate.pdf', ['certificate' => $certificate])->setPaper($customPaper, 'portrait');;
        //return $pdf->stream($certificate_name);
        $pdf->save(public_path() . '/uploads/certificate/' . $certificate_name);
        /** === make pdf certificate ===== */
        $certificate->path =  "/uploads/certificate/$certificate_name";
        $certificate->save();

        $this->showToastrMessage('success', 'Certificate has been saved');
        return redirect()->back();

    }

    public function delete($uuid)
    {
        if (!Auth::user()->can('manage_certificate')) {
            abort('403');
        } // end permission checking

        $certificate = Certificate::whereUuid($uuid)->first();
        $this->deleteFile($certificate->image);
        $this->deleteFile($certificate->role_1_signature);
        $this->deleteFile( $certificate->path);
        $certificate->delete();

        $this->showToastrMessage('error', 'Certificate has been deleted');
        return redirect()->back();
    }


}
