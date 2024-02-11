<?php
namespace App\Repositories;


use App\Models\Member;
use App\Models\User;

class memberRepostory{

    public function createMember(array $request){
        $user = User::create([

            'email' => $request['email'],

        ]);

        $user->assignRole('member');
        $member = new Member();
        $member->email = $request->input('email');
        $member->full_name = $request->input('full_name');
        $member->display_name = $request->input('display_name');
        $member->profile_pic = $request->input('profile_pic');
        $member->gender = $request->input('gender');
        $member->nic = $request->input('nic');
        $member->tel1 = $request->input('tel1');
        $member->tel2 = $request->input('tel2');
        $member->address = $request->input('address');
        $member->is_married = $request->input('is_married');
        $member->is_registered = $request->input('is_registered');
        $member->member_divisions_id = $request->input('member_divisions_id');
        $member->member_parties_id = $request->input('member_parties_id');

        $memberId = User::latest()->first()->id;
        $member->user_id = $memberId;
        $member->save();
        $member->assignRole('member');
        $responce = [
            'user' => $member,
            'type' => $member,

        ];
        return response($responce, 201);
    }

}


