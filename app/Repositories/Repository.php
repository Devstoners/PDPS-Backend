<?php
namespace App\Repositories;
use App\Models\Complain;
use App\Models\news;
use App\Models\News_locales;
use App\Models\Payments;
use App\Models\Tax;
use App\Models\Teachers;
use App\Models\WaterSupply;
use App\Reposi;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;
use Carbon\Carbon;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use Vonage\SMS\Message\SMS;
use Vonage\SMS\Message\SMSCollection;

class Repository{
    public function registerNew($data){
    //return $data;
        $user = User::create([
            'nic' => $data['nic'],
            'email' => $data['email'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'type' => $data['type'],
            'requesttype' => $data['requesttype'],
        ]);
        $token = $user->createToken('apptoken')->plainTextToken;
        switch ($data['requesttype']) {
            case '1':
                $user->assignRole('admin');
                break;
            case '5':
                $user->assignRole('officer');
                break;
            case '2':
                $user->assignRole('gramasewaka');
                break;
            case '4':
                $user->assignRole('member');
                break;
            case '3':
                $user->assignRole('customer');
                break;
            // Add more cases if needed
            default:
                // Handle any other cases or throw an exception if needed
        }

        $responce = [
            'user' => $user,
            'token' => $token,

        ];
        return response($responce, 201);
    }



    public function addNewWater(array $data){

        $user = User::create([
            'name' => $data['aname'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token = $user->createToken('apptoken')->plainTextToken;

     //   $user->assignRole('teacher');

        $adminDetails = User::latest()->first()->id;

        $teacher = WaterSupply::create([
            'tele' => $data['tele'],
            'aname' => $data['aname'],
            'distance' => $data['distance'],
            'taxNo' => $data['taxNo'],
            'scheme' => $data['scheme'],
            'service' => $data['service'],
              'address' => $data['address'],
            'user_id ' => $user->id,
        ]);

        $basic  = new \Vonage\Client\Credentials\Basic("831cb65a", "LVg6EEtYjLd5vJ6A");
        $client = new \Vonage\Client($basic);
        $responsessssss = $client->sms()->send(
            new \Vonage\SMS\Message\SMS("94778590204",  'PDPS', 'Call me if u get this A text message sent using the Nexmo SMS API')
        );
        $message = $responsessssss->current();

        if ($message->getStatus() == 0) {
            return "The message was sent successfully\n ";
        } else {
            return "The message failed with status: " . $message->getStatus() . "\n";
        }

        $recipient = "asanka.lakshitha@gmail.com";
        $subject = "Water Supply Application";
        $messagesss = "Your application submitted Successfully";
        Mail::to($data['email'])->send(new CustomEmail($messagesss));

        $responce = [
            'teacher' => $teacher,
            'token' => $token
        ];
        return $responce;
    }

    public function addNewTax($fields){
        $taxCal = Tax::findbynic($fields->nic);
        if($taxCal){
            $taxCal = Tax::create([
                'name' => $fields['name'],
                'nic' => $fields['nic'],
                'amount' => $fields['amount'],
                'account' => $fields['account'],
                'telephone' => $fields['telephone'],
                'description' => $fields['description'],
                'email' => $fields['email'],
            ]);
            $taxId = Tax::latest()->first();
            $payment = Payments::create([
                'year' => $fields['year'],
                'amount' => $fields['amount'],
                'tax_id' => $taxId,
                'payment_type' => $fields['payment_type']
            ]);
        }else{
       /*     $taxCal->name =  $fields['name'];
            $taxCal->nic =  $fields['nic'];
            $taxCal->ammount =  $fields['ammount'];
            $taxCal->account =  $fields['account'];
            $taxCal->ammount =  $fields['ammount'];
            $taxCal->telephone =  $fields['telephone'];
            $taxCal->description =  $fields['description'];
            $taxCal->save();*/

            $payment = Payments::create([
                'year' => $fields['year'],
                'amount' => $fields['amount'],
                'tax_id' => $taxCal->id,
                'payment_type' => $fields['payment_type']
            ]);
        }

        return [
            'tax' => $payment,
        ];
    }

    public function  login($request)
    {
        //return $request;
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        //check email
        $user = User::whereEmail($fields['email'])->with('roles')->first();


        if (!$user || !Hash::check($fields['password'], $user->password)) {
            throw new NotFoundHttpException('Please check email or Password !');
        } else {
            if ($user->is_active === 0) {
                throw new UnauthorizedHttpException(
                    "Account Is Disabled! Please Contact Administrator.",
                    "Account Is Disabled! Please Contact Administrator."
                );
            }
        }

        $token = $user->createToken('apptoken')->plainTextToken;

        return  $response = [
            'user' => $user,
            'token' => $token,
        ];

//        return response($responce, 201);
//        return response([
//            'message' => $responce
//        ], 200);
    }


    public function getComplainCounts()
    {
        return Complain::count();
    }

    public function complainCount()
    {
        return Complain::count();
    }

}


