<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }



    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|max:255|email',
        ]);
    }

    /**
     * Get a response for an incoming forgot password request.
     *
     * @param  Request  $request (including only $email)
     * @return json response.
     */
    public function index(Request $request)
    {
        $email = $request->email;

        $data = array();
        $status = 200;

        $v = $this->validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $v->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

        try {
            $num_Rows = User::where('email', '=', $email)
                    ->where('status', 1)
                    ->count();

            if ($num_Rows > 0) 
            {
                $userid = User::select('userid')->where('email', '=', $email)
                    ->where('status', 1)
                    ->firstOrFail()->userid;

                $this->sendMail($email, $userid);

                $data = array(
                    'status' => 200,
                    'success' => true,
                    'userid' => $userid,
                    'message' => 'Please check your email to reset password.',
                );
            } 
            else
            {
                $status = 400;
                $data = array(
                    'status' => 400,
                    'success' => false,
                    'message' => 'Incorrect email.',
                );
            }
            
        } catch (ModelNotFoundException $e) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Incorrect email.'
            );
        }
        return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }


    private function sendMail($email, $userid)
    {
        $current_device = "iOS";
        if (isset($device)) {
            $current_device = $device;
        }

        $htmlbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                                <html xmlns="http://www.w3.org/1999/xhtml"><head>
                                <title>Legitkicks</title>
                                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                                <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
                                <style type="text/css">
                                body{margin:0px;padding:0px;text-align:left;}
                                html{width: 100%; }
                                img {border:0px;text-decoration:none;display:block; outline:none;}
                                a,a:hover{color:#FFF;text-decoration:none;}.ReadMsgBody{width: 100%; background-color: #ffffff;}.ExternalClass{width: 100%; background-color: #ffffff;}
                                table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }  
                                img[class=imageScale]{}
                                .divater-bottom{ border-bottom:#eeeff1 solid 1px;}
                                .space1{padding:35px 35px 35px 35px;}
                                .contact-space{padding:15px 24px 15px 24px;}
                                table[class=social]{ text-align:right;}
                                .contact-text{font:Bold 14px Arial, Helvetica, sans-serif; color:#FFF; padding-left:4px;}
                                .border-bg{ border-top:#986258 solid 4px; background:#11BCFA;}
                                .borter-inner-bottom{ border-bottom:#e24851 solid 1px;}
                                .borter-inner-top{ border-top:#f2767d solid 1px;}
                                .borter-footer-bottom{ border-bottom:#ececec solid 1px; border-top:#eb5e66 solid 3px;}
                                .borte-footer-inner-borter{ border-bottom:#cf4850 solid 3px;}
                                .header-space{padding:0px 20px 0px 20px;}
                                @media only screen and (max-width:640px)
                                {
                                body{width:auto!important;}
                                .main{width:440px !important;margin:0px; padding:0px;}
                                .two-left{width:440px !important; text-align: center!important;}
                                .two-left-inner{width: 376px !important; text-align: center!important;}
                                .header-space{padding:30px 0px 30px 0px;}
                                }
                                @media only screen and (max-width:479px)
                                {
                                body{width:auto!important;}
                                .main{width:280px !important;margin:0px; padding:0px;}
                                .two-left{width: 280px !important; text-align: center!important;}
                                .two-left-inner{width: 216px !important; text-align: center!important;}
                                .space1{padding:35px 0px 35px 0px;}
                                table[class=social]{ width:100%; text-align:center; margin-top:20px;}
                                table[class=contact]{ width:100%; text-align:center; font:12px;}
                                .contact-space{padding:15px 0px 15px 0px;}
                                .header-space{padding:30px 0px 30px 0px;}
                                }
                                </style>
                                <style class="firebugResetStyles" type="text/css" charset="utf-8">
                                .firebugResetStyles {
                                z-index: 2147483646 !important;
                                top: 0 !important;
                                left: 0 !important;
                                display: block !important;
                                border: 0 none !important;
                                margin: 0 !important;
                                padding: 0 !important;
                                outline: 0 !important;
                                min-width: 0 !important;
                                max-width: none !important;
                                min-height: 0 !important;
                                max-height: none !important;
                                position: fixed !important;
                                transform: rotate(0deg) !important;
                                transform-origin: 50% 50% !important;
                                border-radius: 0 !important;
                                box-shadow: none !important;
                                background: transparent none !important;
                                pointer-events: none !important;
                                white-space: normal !important;
                                }
                                style.firebugResetStyles {
                                display: none !important;
                                }
                                .firebugBlockBackgroundColor {
                                background-color: transparent !important;
                                }
                                .firebugResetStyles:before, .firebugResetStyles:after {
                                content: "" !important;
                                }
                                .firebugCanvas {
                                display: none !important;
                                }
                                .firebugLayoutBox {
                                width: auto !important;
                                position: static !important;
                                }
                                
                                .firebugLayoutBoxOffset {
                                opacity: 0.8 !important;
                                position: fixed !important;
                                }
                                .firebugLayoutLine {
                                opacity: 0.4 !important;
                                background-color: #000000 !important;
                                }
                                .firebugLayoutLineLeft, .firebugLayoutLineRight {
                                width: 1px !important;
                                height: 100% !important;
                                }
                                .firebugLayoutLineTop, .firebugLayoutLineBottom {
                                width: 100% !important;
                                height: 1px !important;
                                }
                                .firebugLayoutLineTop {
                                margin-top: -1px !important;
                                border-top: 1px solid #999999 !important;
                                }
                                .firebugLayoutLineRight {
                                border-right: 1px solid #999999 !important;
                                }
                                .firebugLayoutLineBottom {
                                border-bottom: 1px solid #999999 !important;
                                }
                                .firebugLayoutLineLeft {
                                margin-left: -1px !important;
                                border-left: 1px solid #999999 !important;
                                }
                                .firebugLayoutBoxParent {
                                border-top: 0 none !important;
                                border-right: 1px dashed #E00 !important;
                                border-bottom: 1px dashed #E00 !important;
                                border-left: 0 none !important;
                                position: fixed !important;
                                width: auto !important;
                                }
                                .firebugRuler{
                                position: absolute !important;
                                }
                                .firebugRulerH {
                                top: -15px !important;
                                left: 0 !important;
                                width: 100% !important;
                                height: 14px !important;
                                border-top: 1px solid #BBBBBB !important;
                                border-right: 1px dashed #BBBBBB !important;
                                border-bottom: 1px solid #000000 !important;
                                }
                                .firebugRulerV {
                                top: 0 !important;
                                left: -15px !important;
                                width: 14px !important;
                                height: 100% !important;
                                border-left: 1px solid #BBBBBB !important;
                                border-right: 1px solid #000000 !important;
                                border-bottom: 1px dashed #BBBBBB !important;
                                }
                                .overflowRulerX > .firebugRulerV {
                                left: 0 !important;
                                }
                                .overflowRulerY > .firebugRulerH {
                                top: 0 !important;
                                }
                                .fbProxyElement {
                                position: fixed !important;
                                pointer-events: auto !important;
                                }
                                a.CSS3_Button {
                                display: inline-block;
                                border: 1px solid #986258;
                                padding: 8px 18px;
                                text-decoration: none;
                                color: #FFFFFF;
                                font-size: 13px;
                                font-weight: bold;
                                font-family: Arial, Helvetica, sans-serif;
                                width:180px;
                                }
                                #Theme-a {
                                background:  #986258;
                                }
                                #Theme-a:hover {
                                background: #986258;
                                }
                                </style>
                                </head>
                                <body>
                                <table class="main-bg" cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
                                  <tbody>
                                    <tr>
                                        <td style="padding:50px 0px 50px 0px;" align="center" valign="top">
                                            <table class="main" cellpadding="0" cellspacing="0" align="center" border="0" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td align="left" valign="top">
                                                            <table class="main" cellpadding="0" cellspacing="0" align="center" border="0" width="600">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="padding:30px 20px 30px 20px;background:#986258;" class="border-bg" align="left" valign="top">
                                                                            <table cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font:normal 12px Arial, Helvetica, sans-serif; color:#FFF; background:#986258;" align="left" valign="middle" width="100%">   
                                                                                                <img src="http://52.0.55.172/jaketv/jaketv_admin/images/jakelogo.png" style="width:100px; float:left;">
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top">
                                                            <table class="main" cellpadding="0" cellspacing="0" border="0" width="600">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="background-color:#f7f7f7; text-align:justify; font:normal 15px Arial, Helvetica, sans-serif;line-height:18px; padding:20px 25px 20px 25px;" valign="top">
                                                                         Hi, <br/> <br/> Please click on below link to reset your JAKETV account password.
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" valign="top">
                                                            <table class="main" cellpadding="0" cellspacing="0" border="0" width="600">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="background-color:#f7f7f7; text-align:center; font:normal 16px Arial, Helvetica, sans-serif;line-height:18px; padding-top:0px; padding-bottom:15px;" align="left" bgcolor="#ffffff" valign="top">
                                                                        <a href="'.$sitefront.'headerlocation.php?id='.$userid.'&device='.$current_device.'" class="CSS3_Button" id="Theme-a" style="padding:10px 20px; background:#986258; color:#fff;">Reset Password</a>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="background-color:#986258; padding:16px 0px 14px 0px;border-bottom:#986258 solid 3px;text-align:center;" class="borte-footer-inner-borter" align="left"  valign="top">
                                                                            <table cellpadding="0" cellspacing="0" align="center" border="0" width="204">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <span style="color:#ffffff;">Copyright © 2015 Ohryta</span>
                                                                                        <!--<td align="left" valign="top" width="34"><a href="#"><img src="RidioMail_files/facebook-icon.png" alt="" height="28" width="27"></a></td>
                                                                                        <td align="left" valign="top" width="34"><a href="#"><img src="RidioMail_files/twitter-icon.png" alt="" height="28" width="27"></a></td>
                                                                                        <td align="left" valign="top" width="34"><a href="#"><img src="RidioMail_files/google-icon.png" alt="" height="28" width="27"></a></td>
                                                                                        <td align="left" valign="top" width="34"><a href="#"><img src="RidioMail_files/rss-icon.png" alt="" height="28" width="27"></a></td>
                                                                                        <td align="left" valign="top" width="34"><a href="#"><img src="RidioMail_files/dripple-icon.png" alt="" height="28" width="27"></a></td>
                                                                                        <td align="left" valign="top" width="34"><a href="#"><img src="RidioMail_files/youtube-icon.png" alt="" height="28" width="27"></a></td>-->
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                                </body>
                                </html>';
                           
        // $subject = 'RESET PASSWORD';
        // require '../jaketv_admin/phpmailer/PHPMailerAutoload.php';
        // $mail = new PHPMailer;
        // $mail->isSMTP(); 
        // $mail->Host = 'email-smtp.us-east-1.amazonaws.com';    
        // $mail->SMTPAuth = true;
        // $mail->Username = 'AKIAI5ZAPNKQLL4BAU4A';
        // $mail->Password = 'AgNq6Yff6CAp3i2HcPwdteRIUVzg17HV9bwp3acGckq+';
        /*$mail->Username = 'shivani.hjdimensions@gmail.com';
        $mail->Password = 'Shivani@123#';*/
        // $mail->SMTPSecure = 'tls';
        // $mail->From = 'jaketvmanager@gmail.com';
        // $mail->FromName = 'JAKETV';
        // $mail->addAddress($email);
        // $mail->isHTML(true);
        // $mail->Subject = $subject;
        // $mail->Body = $htmlbody;
        // $mail->send();



        $data = array('name'=>"Virat Gandhi");
        Mail::send('Auth/mail', $data, function($message) {
            $message->to('$email', 'Tutorials Point')->subject('Laravel HTML Testing Mail');
         $message->from('jaketvmanager@gmail.com','Virat Gandhi');
        });
        echo "HTML Email Sent. Check your inbox.";
    }

}
