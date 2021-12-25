<?php

namespace App\Http\Controllers;

use App\email;
use App\Mail\Gmail;
use App\Imports\EmailImport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


class EmailController extends Controller
{

    public function sendEmailAutomatic()
    {
        // email::query()->update(['send'=>'0']);
        
        $emails = email::all();
        
        if ($emails->count() >=1 ) {    
            foreach ($emails as $value) {
                $send = Mail::To($value->email)->send(new Gmail());
                if ($send) {
                    email::where('id',$value->id)
                    ->update([
                        'send'=>'1'
                    ]);
                    sleep(3);
                } else {
                    echo 'not send';
                    exit;
                }
            }

            return response()->json([
                "status"    => '1',
                "msg"       => 'Send to all successfully'
            ]);
        } else {
          return response()->json([
                "status"    => '0',
                "msg"       => 'No emails found please add some files'
            ]);  
        }
       
    }

    public function getEmailsNumber()
    {

        return email::all()->count();
    }


    public function showData()
    {
        $sql_get = email::all();
        $counter=1;
        
        
        $data = '<div class="row">';
        foreach ($sql_get as  $value) {
            if ($counter < 10) {
                $counter = '0'.$counter;
            }
            $data .= "<div class='col-12' id='$value->id'> ";
            $data .= "<div class='row'>";
            $data .= '<div class="col-10 border-bottom mb-3 pb-2">'.$counter++."- ".$value->email.'</div>';
            $data .= ' <div class="col-2 text-right mb-3 pb-2 border-bottom text-danger" ><i class="far fa-trash-alt delete-btn" data-token="'.csrf_token().'" data-id="'.$value->id.'"></i></div>';
            $data .= '</div>';
            $data .= '</div>';
        }
        $data.="</div>
        <script>
            $('.delete-btn').on('click',function (e) {
                e.preventDefault();
                var token = $(this).data('token');
                var id = $(this).data('id');
                Swal.fire({
                    icon: 'question',
                    title: 'Do you want to Delete this email?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                        url: '/emails/deleteone',
                        method: 'post',
                        data: {
                        '_token': token,
                        'id' : id
                        },
                        beforeSend: function () {
                            $('.loader').css({
                            'opacity':'.9',
                            'display':'block'
                            });
                        },
                        success: function (data) {
                            if (data.status == '1') {
                            Swal.fire({
                                title: 'Successfully',
                                text: data.msg,
                                icon: 'success',
                                padding: '2em'
                            })
                            $('#'+id).remove();
                            } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.msg,
                                icon: 'error',
                                padding: '2em'
                            })

                            }
                        $('.loader').css({
                            'opacity':'.9',
                            'display':'none'
                            });
                        },

                        })
                    } 
                    })
                
            })</script>
        ";
        return $data;
    }

    public function addFilesToCurrent(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        

        $up = Excel::import(new EmailImport,request()->file('file'));
        if ($up) {
            return response()->json([
                "status"    => '1',
                "msg"       => 'Upload successfully'
            ]);
        } else {
            return response()->json([
                "status"    => '0',
                "msg"       => 'Sorry, please try again'
            ]);
        }
    }

    public function uploadScrach(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
     
        $sliderDelete = email::truncate();
        if ($sliderDelete) {
            $up = Excel::import(new EmailImport,request()->file('file'));
            if ($up) {
                return response()->json([
                    "status"    => '1',
                    "msg"       => 'Upload successfully'
                ]);
            } else {
                return response()->json([
                    "status"    => '0',
                    "msg"       => 'Sorry, please try again'
                ]);
            }
        } else {
            return response()->json([
                    "status"    => '0',
                    "msg"       => 'Sorry, please try again'
                ]);
        }
    }

    public function deleteEmail(Request $request)
    {
         
        $sliderDelete = email::truncate();
        
        if ($sliderDelete)
            return response()->json([
                "status"    => '1',
                "msg"       => 'Deleted successfully'
            ]);
        else
            return response()->json([
                "status"    => '0',
                "msg"       => 'Sorry, please try again'
            ]);
    }

    public function deleteOne(Request $request)
    {
         
        $sliderDelete = email::destroy($request->id);
        
        if ($sliderDelete)
            return response()->json([
                "status"    => '1',
                "msg"       => 'Deleted successfully'
            ]);
        else
            return response()->json([
                "status"    => '0',
                "msg"       => 'Sorry, please try again'
            ]);
    }
}
