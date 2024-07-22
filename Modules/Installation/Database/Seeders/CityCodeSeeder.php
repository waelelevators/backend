<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CityCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            ['name' => 'Riyadh', 'city_code' => 'RUH'],
            ['name' => 'Jeddah', 'city_code' => 'JED'],
            ['name' => 'Mecca', 'city_code' => 'MEC'],
            ['name' => 'Medina', 'city_code' => 'MED'],
            ['name' => 'Dammam', 'city_code' => 'DMM'],
            ['name' => 'Khobar', 'city_code' => 'KHB'],
            ['name' => 'Abha', 'city_code' => 'AHB'],
            ['name' => 'Tabuk', 'city_code' => 'TUU'],
            ['name' => 'Hail', 'city_code' => 'HAS'],
            ['name' => 'Jazan', 'city_code' => 'GIZ'],
            ['name' => 'Najran', 'city_code' => 'EAM'],
            ['name' => 'Al Bahah', 'city_code' => 'ABT'],
            ['name' => 'Al Jawf', 'city_code' => 'AJF'],
            ['name' => 'Arar', 'city_code' => 'RAE'],
            ['name' => 'Al Kharj', 'city_code' => 'AKH'],
            ['name' => 'Yanbu', 'city_code' => 'YNB'],
            ['name' => 'Qatif', 'city_code' => 'QAT'],
            ['name' => 'Buraydah', 'city_code' => 'BUR'],
            ['name' => 'Unaizah', 'city_code' => 'UNA'],
            ['name' => 'Sakakah', 'city_code' => 'SAD'],
            ['name' => 'Ras Tanura', 'city_code' => 'RAS'],
            ['name' => 'Dhahran', 'city_code' => 'DHA'],
            ['name' => 'Al Khafji', 'city_code' => 'KFA'],
            ['name' => 'Hafar Al-Batin', 'city_code' => 'HBT'],
            ['name' => 'Al Jubail', 'city_code' => 'JBI'],
            ['name' => 'Al Qunfudhah', 'city_code' => 'AQD'],
            ['name' => 'Al Lith', 'city_code' => 'ALY'],
            ['name' => 'Al Majmaah', 'city_code' => 'MAJ'],
            ['name' => 'Bisha', 'city_code' => 'BHH'],
            ['name' => 'Al Mubarraz', 'city_code' => 'MBR'],
            ['name' => 'Al Hasa', 'city_code' => 'HSA'],
            ['name' => 'Al Aflaj', 'city_code' => 'AFL'],
            ['name' => 'Al Bukayriyah', 'city_code' => 'BUK'],
            ['name' => 'Al Duwadimi', 'city_code' => 'DWD'],
            ['name' => 'Al Ghat', 'city_code' => 'GHT'],
            ['name' => 'Al Qasab', 'city_code' => 'QSB'],
            ['name' => 'Al Uyaynah', 'city_code' => 'UYN'],
            ['name' => 'Al Zulfi', 'city_code' => 'ZUL'],
            ['name' => 'Badr', 'city_code' => 'BAD'],
            ['name' => 'Baljurashi', 'city_code' => 'BAL'],
            ['name' => 'Dhurma', 'city_code' => 'DHR'],
            ['name' => 'Hotat Bani Tamim', 'city_code' => 'HBT'],
            ['name' => 'Huraymila', 'city_code' => 'HRM'],
            ['name' => 'Layla', 'city_code' => 'LAY'],
            ['name' => 'Muzahmiyya', 'city_code' => 'MUZ'],
            ['name' => 'Rafha', 'city_code' => 'RAF'],
            ['name' => 'Riyadh Al Khabra', 'city_code' => 'RAK'],
            ['name' => 'Samtah', 'city_code' => 'SAM'],
            ['name' => 'Sayhat', 'city_code' => 'SYH'],
            ['name' => 'Shaqra', 'city_code' => 'SHQ'],
            ['name' => 'Sibai', 'city_code' => 'SIB'],
            ['name' => 'Tathlith', 'city_code' => 'TTH'],
            ['name' => 'Turubah', 'city_code' => 'TRB'],
            ['name' => 'Umluj', 'city_code' => 'UMJ'],
            ['name' => 'Wadi Al-Dawasir', 'city_code' => 'WAD'],
            ['name' => 'Al Wajh', 'city_code' => 'WAJ'],
            ['name' => 'Zulfi', 'city_code' => 'ZLF'],
            // Add other cities and their codes here as needed
        ];

        foreach ($cities as $city) {
            DB::table('cities')
                ->where('name', $city['name'])
                ->update(['city_code' => $city['city_code']]);
        }
    }
}
