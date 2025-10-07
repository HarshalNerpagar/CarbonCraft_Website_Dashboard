<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterSettings;

class MasterControlSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new MasterSettings();
        $site = $settings->siteData();
        $site['default_currency'] = '₹';
        $site['default_application_name'] = 'CarbonCraft';
        $site['default_phone_number'] = '+91 98765 43210';
        $site['default_tax_percentage'] = '18';
        $site['default_state'] = 'Maharashtra';
        $site['default_city'] = 'Mumbai';
        $site['default_country'] = 'IN';
        $site['default_zip_code'] = '400001';
        $site['default_address'] = 'CarbonCraft Studio, Mumbai, India';
        $site['store_email'] = 'contact@carboncraft.in';
        $site['store_tax_number'] = 'GSTIN123456789';
        $site['default_printer'] = '1';
        $site['forget_password_enable'] = 1;
        $site['country_code'] = +91;
        $site['default_currency_alignment'] = 1;
        $site['sms_createorder'] = 'Hi <name> An Order #<order_number> was created and will be delivered on <delivery_date> Your Order Total is <total>.';
        $site['sms_statuschange'] = 'Hi <name> Your Order #<order_number> status has been changed to <status> on <current_time>';
        foreach ($site as $key => $value) {
            MasterSettings::updateOrCreate(['master_title' => $key],['master_value' => $value]);
        }

        //run permission seeder
        $this->call(PermissionSeeder::class);
    }
}
