<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTaxPropertiesTable extends Command
{
    protected $signature = 'tax:create-properties-table';
    protected $description = 'Create the tax_properties table if it does not exist';

    public function handle()
    {
        $this->info('🔍 Checking tax_properties table...');

        if (Schema::hasTable('tax_properties')) {
            $this->info('✅ tax_properties table already exists!');
            $count = DB::table('tax_properties')->count();
            $this->info("Records in table: $count");
            return;
        }

        $this->info('❌ tax_properties table does not exist!');
        $this->info('Creating tax_properties table...');

        try {
            Schema::create('tax_properties', function ($table) {
                $table->id();
                $table->timestamps();
                $table->integer('division_id');
                $table->integer('tax_payee_id');
                $table->string('street', 255);
                $table->integer('property_type');
                $table->string('property_name', 255);
                $table->boolean('property_prohibition')->default(0);
            });

            $this->info('✅ tax_properties table created successfully!');
            
            // Mark the migration as run
            DB::table('migrations')->insert([
                'migration' => '2024_04_02_094213_create_tax_properties_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            
            $this->info('✅ Migration marked as run!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error creating table: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
