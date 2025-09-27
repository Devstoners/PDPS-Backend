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
// Hall Management
        $addHall = 'add hall';
        $editHall = 'edit hall';
        $deleteHall = 'delete hall';
        $addFacility = 'add facility';
        $editFacility = 'edit facility';
        $deleteFacility = 'delete facility';
        $addHallFacility = 'add hall facility';
        $editHallFacility = 'edit hall facility';
        $deleteHallFacility = 'delete hall facility';
        $addHallRate = 'add hall rate';
        $editHallRate = 'edit hall rate';
        $deleteHallRate = 'delete hall rate';
        $addHallCustomer = 'add hall customer';
        $editHallCustomer = 'edit hall customer';
        $deleteHallCustomer = 'delete hall customer';
        $addHallReservation = 'add hall reservation';
        $editHallReservation = 'edit hall reservation';
        $deleteHallReservation = 'delete hall reservation';
        $addHallCustomerPayment = 'add hall customer payment';
        $addHallFacilityRate = 'add hall facility rate';
        $editHallFacilityRate = 'edit hall facility rate';
        $deleteHallFacilityRate = 'delete hall facility rate';      


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


        Permission::create(['name' => $addHall]);
        Permission::create(['name' => $editHall]);
        Permission::create(['name' => $deleteHall]);
        Permission::create(['name' => $addFacility]);
        Permission::create(['name' => $editFacility]);
        Permission::create(['name' => $deleteFacility]);
        Permission::create(['name' => $addHallFacility]);
        Permission::create(['name' => $editHallFacility]);
        Permission::create(['name' => $deleteHallFacility]);
        Permission::create(['name' => $addHallRate]);
        Permission::create(['name' => $editHallRate]);
        Permission::create(['name' => $deleteHallRate]);
        Permission::create(['name' => $addHallCustomer]);
        Permission::create(['name' => $editHallCustomer]);
        Permission::create(['name' => $deleteHallCustomer]);
        Permission::create(['name' => $addHallReservation]);
        Permission::create(['name' => $editHallReservation]);
        Permission::create(['name' => $deleteHallReservation]);
        Permission::create(['name' => $addHallCustomerPayment]);
        Permission::create(['name' => $addHallFacilityRate]);
        Permission::create(['name' => $editHallFacilityRate]);
        Permission::create(['name' => $deleteHallFacilityRate]);

        Permission::create(['name' => $completeService]);
        Permission::create(['name' => $requestService]);

        Permission::create(['name' => $addBackup]);

        $admin = 'admin';
        $secretary = 'secretary';
        $president = 'president';
        $officerWaterBill = 'officerWaterBill';
        $officerTax = 'officerTax';
        $officerHallReserve = 'officerHallReserve';
        $officerTaxAssess = 'officerTaxAssess';
        $meterReader = 'meterReader';
        $officer = 'officer';
        $member = 'member';
        $customerWaterBill = 'customerWaterBill';
        $customerTax = 'customerTax';
        $customerHallReserve = 'customerHallReserve';
        $supplier = 'supplier';


        //Role::create(['name' => $admin])->givePermissionTo(Permission::all());
        Role::create(['name' => $admin])->givePermissionTo(
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
            $addHall,
            $editHall,
            $deleteHall,
            $addFacility,
            $editFacility,
            $deleteFacility,
            $addHallFacility,
            $editHallFacility,
            $deleteHallFacility,
            $addHallRate,
            $editHallRate,
            $deleteHallRate,
            $addHallCustomer,
            $editHallCustomer,
            $deleteHallCustomer,
            $addHallReservation,
            $editHallReservation,
            $deleteHallReservation,
            $addHallCustomerPayment,
            $addHallFacilityRate,
            $editHallFacilityRate,
            $deleteHallFacilityRate,
        );

        // Secretary (Type 2)
        Role::create(['name' => $secretary])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // President (Type 3)
        Role::create(['name' => $president])->givePermissionTo(
            $addComplainAction,
        );

        // OfficerWaterBill (Type 4)
        Role::create(['name' => $officerWaterBill])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // OfficerTax (Type 5)
        Role::create(['name' => $officerTax])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // OfficerHallReserve (Type 6)
        Role::create(['name' => $officerHallReserve])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // OfficerTaxAssess (Type 7)
        Role::create(['name' => $officerTaxAssess])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // MeterReader (Type 8)
        Role::create(['name' => $meterReader])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // Officer (Type 9)
        Role::create(['name' => $officer])->givePermissionTo(
            $addComplainAction,
            $completeService,
            $confirmPayment,
        );

        // Member (Type 10)
        Role::create(['name' => $member])->givePermissionTo(
            $addComplainAction,
        );

        // CustomerWaterBill (Type 11)
        Role::create(['name' => $customerWaterBill])->givePermissionTo(
            $addComplain,
            $addPayment,
            $requestService,
        );

        // CustomerTax (Type 12)
        Role::create(['name' => $customerTax])->givePermissionTo(
            $addComplain,
            $addPayment,
            $requestService,
        );

        // CustomerHallReserve (Type 13)
        Role::create(['name' => $customerHallReserve])->givePermissionTo(
            $addComplain,
            $addPayment,
            $requestService,
        );

        // Supplier (Type 14)
        Role::create(['name' => $supplier])->givePermissionTo(
            $registerSupplier,
            $bidTender,
        );

    }
}
