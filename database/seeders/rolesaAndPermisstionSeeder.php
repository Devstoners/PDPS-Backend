<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class rolesaAndPermisstionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        $addUser = 'add user';
        $editUser = 'edit user';
        $deleteUser = 'delete user';

        $addNews = 'add news';
        $editNews = 'edit news';
        $deleteNews = 'delete news';

        $addProject = 'add project';
        $editProject = 'edit project';
        $deleteProject = 'delete project';

        $addGallery = 'add gallery';
        $editGallery = 'edit gallery';
        $deleteGallery = 'delete gallery';

        $addComplain = 'add complain';
        $editComplain = 'edit complain';
        $deleteComplain = 'delete complain';

        $addComplainAction = 'add complain action';
        $editComplainAction = 'edit complain action';
        $deleteComplainAction = 'delete complain action';

        $addOfficerPosition = 'add officer position';
        $editOfficerPosition = 'edit officer position';
        $deleteOfficerPosition = 'delete officer position';

        $addOfficerSubject = 'add officer subject';
        $editOfficerSubject = 'edit officer subject';
        $deleteOfficerSubject = 'delete officer subject';

        $addMemberDivision = 'add member division';
        $editMemberDivision = 'edit member division';
        $deleteMemberDivision = 'delete member division';

        $addMemberParty = 'add member party';
        $editMemberParty = 'edit member party';
        $deleteMemberParty = 'delete member party';

        $addMemberPosition = 'add member position';
        $editMemberPosition = 'edit member position';
        $deleteMemberPosition = 'delete member position';

        $addGsDivision = 'add gs division';
        $editGsDivision = 'edit gs division';
        $deleteGsDivision = 'delete gs division';

        $registerSupplier = 'register supplier';
        $editSupplier = 'edit supplier';
        $deleteSupplier = 'delete supplier';

        $addTender = 'add tender';
        $editTender = 'edit tender';
        $deleteTender = 'delete tender';
        $bidTender = 'bid tender';

        $addPayment = 'add payment';
        $confirmPayment = 'confirm payment';

        $addBackup = 'add backup';

        $requestService = 'request service';
        $completeService = 'complete service';


        Permission::create(['name' => $addUser]);
        Permission::create(['name' => $editUser]);
        Permission::create(['name' => $deleteUser]);

        Permission::create(['name' => $addNews]);
        Permission::create(['name' => $editNews]);
        Permission::create(['name' => $deleteNews]);

        Permission::create(['name' => $addProject]);
        Permission::create(['name' => $editProject]);
        Permission::create(['name' => $deleteProject]);

        Permission::create(['name' => $addGallery]);
        Permission::create(['name' => $editGallery]);
        Permission::create(['name' => $deleteGallery]);

        Permission::create(['name' => $addComplain]);
        Permission::create(['name' => $editComplain]);
        Permission::create(['name' => $deleteComplain]);

        Permission::create(['name' => $addOfficerPosition]);
        Permission::create(['name' => $editOfficerPosition]);
        Permission::create(['name' => $deleteOfficerPosition]);

        Permission::create(['name' => $addOfficerSubject]);
        Permission::create(['name' => $editOfficerSubject]);
        Permission::create(['name' => $deleteOfficerSubject]);

        Permission::create(['name' => $addMemberDivision]);
        Permission::create(['name' => $editMemberDivision]);
        Permission::create(['name' => $deleteMemberDivision]);

        Permission::create(['name' => $addMemberParty]);
        Permission::create(['name' => $editMemberParty]);
        Permission::create(['name' => $deleteMemberParty]);

        Permission::create(['name' => $addMemberPosition]);
        Permission::create(['name' => $editMemberPosition]);
        Permission::create(['name' => $deleteMemberPosition]);

        Permission::create(['name' => $addGsDivision]);
        Permission::create(['name' => $editGsDivision]);
        Permission::create(['name' => $deleteGsDivision]);

        Permission::create(['name' => $addPayment]);
        Permission::create(['name' => $confirmPayment]);

        Permission::create(['name' => $addComplainAction]);
        Permission::create(['name' => $editComplainAction]);
        Permission::create(['name' => $deleteComplainAction]);

        Permission::create(['name' => $registerSupplier]);
        Permission::create(['name' => $editSupplier]);
        Permission::create(['name' => $deleteSupplier]);

        Permission::create(['name' => $addTender]);
        Permission::create(['name' => $editTender]);
        Permission::create(['name' => $deleteTender]);
        Permission::create(['name' => $bidTender]);




        Permission::create(['name' => $completeService]);
        Permission::create(['name' => $requestService]);

        Permission::create(['name' => $addBackup]);

        $superAdmin = 'admin';
        $officer = 'officer';
        $member = 'member';
        $GS = 'gramasewaka';
        $customer = 'customer';
        $supplier = 'supplier';


        //Role::create(['name' => $superAdmin])->givePermissionTo(Permission::all());
        Role::create(['name' => $superAdmin])->givePermissionTo(
            $addUser,
            $editUser,
            $deleteUser,
            $addNews,
            $editNews,
            $deleteNews,
            $addProject,
            $editProject,
            $deleteProject,
            $addGallery,
            $editGallery,
            $deleteGallery,
            $editComplain,
            $deleteComplain,
            $addOfficerPosition,
            $editOfficerPosition,
            $deleteOfficerPosition,
            $addOfficerSubject,
            $editOfficerSubject,
            $deleteOfficerSubject,
            $addMemberDivision,
            $editMemberDivision,
            $deleteMemberDivision,
            $addMemberParty,
            $editMemberParty,
            $deleteMemberParty,
            $addMemberPosition,
            $editMemberPosition,
            $deleteMemberPosition,
            $addGsDivision,
            $editGsDivision,
            $deleteGsDivision,
            $addBackup,
        );

        Role::create(['name' => $officer])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        Role::create(['name' => $customer])->givePermissionTo(
            $addComplain,
            $addPayment,
            $requestService,
        );

        Role::create(['name' => $member])->givePermissionTo(
            $addComplainAction,
        );

        Role::create(['name' => $supplier])->givePermissionTo(
            $registerSupplier,
            $bidTender,
        );

        Role::create(['name' => $GS]);

    }
}
