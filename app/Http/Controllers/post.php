<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\loginRequest;
use App\Http\Requests\updatecheck;
use App\Http\Requests\imgcheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;
use App\Mail\EmailResetMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Models\ClientVerify;
use App\Models\Client;
use App\Models\Image;
use App\Models\imguser;

use PhpParser\Node\Stmt\Return_;
use Carbon\Carbon;
use Mail;

class post extends Controller
{
    //signup function create account of user with the help of input feilds 
    //and send verification mail to user to verify email account
    // and update verify and email verified at  feilds
    public function signup(StorePostRequest $req)
    {
        $name=$req->input('name');
        $age=$req->input('age');
        $picture=$req->file('picture')->store('apiDocs');
        $email=$req->input('email');
        $req->merge(['password' => Hash::make($req->newPassword)]);
        $password=$req->input('password');
        $token=Str::random(40); 
        DB::table('user')->insert(['name'=>$name,'age'=>$age,'picture'=>$picture,'email'=>$email,'password'=>$password,'token'=>$token]);
        $user = DB::table('user')->where('email', $req->email)->first();
        if(Mail::to($email)->send(new EmailVerificationMail($user)))
        {
            return response()->json([
            'message' => 'Registration Sucessfull please check your Mail for Email Confirmation'], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Account not created'], 200);
        }
    }
    //login cross check email and password and logged in user account
    public function login(loginRequest $request)
    {
        $email=$request->input('email');
        $password=$request->input('password');
        $user = DB::table('user')->where('email', $request->email)->first();
        $pass=$user->password;
        if($user->email_verified_at !="")
      {
          $message='Email not verified. First go to ur gmail for verify link then login';
      }
      else{
        if($user)
        {
            $answer=Hash::check($request->password, $user->password);
            if($answer)
            {
                 
          
                $value=200;
                $status='Success';
                $token=Str::random(40);
                $get=ClientVerify::create([
                    'user_id' => $user->id, 
                    'remember_me' => $token
                ]); 
                DB::table('user')->where('token', user->token)->update(['token' =>$token]);
                $message='Account Login Successfully';
            }
            else
            {
                
                $value=200;
                $status='error';
                $message='Password not matched';
            }
        }
        else
        {
            $value=200;
            $status='error';
            $message='Account not exist';
        }
    }
       return response()->json(['status'=>$status,'message'=>$message],$value); 
    }
    //update the infromation that is given by the user
    public function update(updatecheck $request,$email)
    {
      $user = DB::table('user')->where('email', $email)->first();
      if($user)
      {
        if($request->has('name'))
        {
            $name=$request->name;    
            DB::table('user')->where('email', $email)->update(['name' =>$name]);
        }
        if($request->has('age'))
        {
            $age=$request->age; 
            DB::table('user')->where('email', $email)->update(['age' =>$age]);  
        }
        if($request->has('password'))
        {
            $password=$request->password; 
            DB::table('user')->where('email', $email)->update(['password' =>$password]);  
        }
        if($request->has('picture'))
        {
            $picture=$request->picture;    
            DB::table('user')->where('email', $email)->update(['picture' =>$picture]);
        }
        if($request->has('email'))
        {
            $email=$request->email; 
            DB::table('user')->where('email', $email)->update(['email' =>$email]);   
        }
        $value=200;
        $status='Success';
        $message='Information Updated';
      }
      else
      {
            $value=200;
            $status='error';
            $message='Account not exist';
      }
      return response()->json(['status'=>$status,'message'=>$message],$value);     
    }
    //This function verify  email and update the feilds in database
    public function verify_email($token)
    {
        $user = DB::table('user')->where('token', $token)->first();
        if(!$user)
        {
            return \redirect(route('home'))->with('error', 'Invalid URL');
        }
        else
        {
            if($user->email_verified_at)
            {
                return \redirect(route('home'))->with('error', 'Email Already Verified');
            }
            else
            {
                DB::table('user')->where('email', $user->email)->update(['email_verified_at' =>\Carbon\Carbon::now()]);
                return \redirect(route('home'))->with('success', 'registered successfully');
            }
        }
        return response()->json([
            'message' => ' verification Sucessfull',

        ], 200);
    }
    //this function send password through mail and user use that password to logged in
    public function forget($email)
    { 
        $password=Str::random(10);
        DB::table('user')->where('email', $email)->update(['password' =>$password]);  
        $user = DB::table('user')->where('email', $email)->first();
        if(Mail::to($user->email)->send(new EmailResetMail($user)))
        {
             return response()->json([
             'message' => 'please check your Mail for password reset'], 200);
        }
        else
        {
             return response()->json([
            'message' => 'please try again'], 200); 
        }
    }
    // This check that if the user is logged in and then upload image in the database  
    //and extract image name and extension from path 
    public function upload(imgcheck $req)
    {
        $token = $request->header('Authorization');
        $user=ClientVerify::where('remember_me',$token)->first()->client;
        $path=$req->file('img')->store('apiDocs');
        $name = basename($path);
        $extension = $req->file('img')->extension();
        $time = \Carbon\Carbon::now();
        $date = date('Y-m-d H:i:s');
        if($req->has('status'))
        {
            $status=$req->input('status');
        }
        else
        {
            $status="hidden";
        }
        $user = DB::table('user')->where('email', $email)->first();
        $uid=$user->id;
        if(DB::table('img')->insert(['name'=>$name,'extension'=>$extension,'date'=>$date,'time'=>$time,'status'=>$status,'uid'=>$uid,'email'=>$email,'path'=>$path]))
        {
            $data = DB::table('img')->where('email', $email and 'name',$name)->first();
            if($data)
            {
                $link=$email.'/'.$name;
                return response()->json([
                    'message' => 'Picture Uploaded','link'=>$link], 200);
            }
            return response()->json([
                'message' => 'Picture Uploaded'], 200);
        }
        else
        {
                return response()->json([
                    'message' => 'picture not uploaded'], 200);
        }
    }
    //this function link view
    public function linkview(Request $request)
    {
        $id=$request->id;
         $link="http://localhost:8000/api/share/.$id";
         return response()->json($link);
    }
    //this will check if the user is authenticate for status or not
    public function verifyimage(Request $request)
    {
        $email=$request->query('email');
        $token = $request->header('Authorization');
        $user=ClientVerify::where('remember_me',$token)->first()->client;
        $User=$user->where('email',$email);
        if($User){
         $id=$request->query('id');
         $data= Image::where('id',$id)->first;
         $user->image()->attach($id); 
         return response()->json($data);
        }
        else
        {
               $message = "image preview not allowed";
               return response()->json($message);
        }
    }
    // This function delete the picture  which the user requested to delete
    public function delete(Request $req)
    {
        if($data = DB::table('img')->where('name', $req->name)->first())
        {
            $token = $request->header('Authorization');
            $user=ClientVerify::where('remember_me',$token)->first()->Client;
            $id=data->id;
            if(DB::table('img')->where('email', $email)->delete(['id' =>$id]))
            {
                $message="Image Deleted Successfully";
            } 
            else
            {
                 $message="not deleted";
            } 
        }
        else
        {
            $message="not deleted";
        }

        return response()->json(['message' => $message], 200);     
    }
    // This function display all the images the user requested to delete
    public function list($email)
    {
        $data = DB::table('user')->where('email',$email)->first();
        if($data)
        {
            $id=$data->id;
            $check = DB::table('img')->where('uid',$id)->first();
            if($check)
            {
                $message="Picture found";
                $picture=$check->path;
            }
        }
        else{
            $message="something went wrong";
            $picture=nothing;
        }
        return response()->json(['message' => $message,'picture'=>$picture], 200);    
    }
//This function search image by date,time,extension,name and 
//delete th picture the user requestd to delete
    public function search($email,Request $request)
    {
       if($request->has('name'))
       {
        $data = DB::table('img')->where('email',$email and 'name',$request->name)->first();
        if($data)
        {
            $result=$data;
            $message="Record found";
        }
        else
        {
            $message="Record  Not found";
            $result;
        }
       }
       if($request->has('date'))
       {
                $data = DB::table('img')->where('email',$email and 'date',$request->date)->first();
            if($data)
            {
                $result=$data;
                $message="Record found";
            }
            else
            {
                $message="Record  Not found";
                $result;
         }
       }
       if($request->has('time'))
       {
            $data = DB::table('img')->where('email',$email and 'time',$request->time)->first();
             if($data)
            {
                $result=$data;
                $message="Record found";
            }
            else
            {
                $message="Record  Not found";
                $result;
            }
       }
       if($request->has('extension'))
       {
            $data = DB::table('img')->where('email',$email and 'extension',$request->extension)->first();
            if($data)
            {
                $result=$data;
                $message="Record found";
            }
             else
             {
                $message="Record  Not found";
                $result;
            }

       }
       if($request->has('status'))
       {
            $data = DB::table('img')->where('email',$email and 'status',$request->status)->first();
            if($data)
            {
                $result=$data;
                $message="Record found";
            }
            else
            {
                $message="Record  Not found";
                $result;
            }
       }
       return response()->json(['message'=>$message], 200);

    }



}
