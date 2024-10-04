<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficerServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('officer_services')->insert([
            [
                'sname_en' => 'Sri Lanka Administrative Service',
                'sname_si' => 'ශ්‍රී ලංකා පරිපාලන සේවය',
                'sname_ta' => 'இலங்கை நிர்வாக சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Engineering Service',
                'sname_si' => 'ශ්‍රී ලංකා ඉංජිනේරු සේවය',
                'sname_ta' => 'இலங்கை பொறியியல் சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Accountants\' Service',
                'sname_si' => 'ශ්‍රී ලංකා ගණකාධිකාරි සේවය',
                'sname_ta' => 'இலங்கை கணக்காளர்கள் சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Planning Service',
                'sname_si' => 'ශ්‍රී ලංකා ක්‍රමසම්පාදන සේවය',
                'sname_ta' => 'இலங்கை திட்டமிடல் சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Scientific Service',
                'sname_si' => 'ශ්‍රී ලංකා විද්‍යාත්මක සේවය',
                'sname_ta' => 'இலங்கை விஞ்ஞான சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Architectural Service',
                'sname_si' => 'ශ්‍රී ලංකා වාස්තු විද්‍යාත්මක සේවය',
                'sname_ta' => 'இலங்கை கட்டிட நிர்மாண சேவை',
            ],
            [
                'sname_en' => 'Management Service Officers\' Service',
                'sname_si' => 'කළමනාකරණ සේවා නිලධාරී සේවය',
                'sname_ta' => 'முகாமைத்துவ சேவை உத்தியோகத்தர் சேவை',
            ],
            [
                'sname_en' => 'Development Officers\' Service',
                'sname_si' => 'සංවර්ධන නිලධාරී සේවය',
                'sname_ta' => 'அபிவிருத்தி உத்தியோகத்தர் சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Librarians\' Service',
                'sname_si' => 'ශ්‍රී ලංකා රජයේ පුස්තකාලයාධිපති සේවය',
                'sname_ta' => 'இலங்கை நூலகர் சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Information & Communication Technology Service',
                'sname_si' => 'ශ්‍රී ලංකා තොරතුරු හා සන්නිවේදන තාක්ෂණ සේවය',
                'sname_ta' => 'இலங்கை தகவல் தொடர்பாடல் தொழில்நுட்பச் சேவை',
            ],
            [
                'sname_en' => 'Government Translators\' Service',
                'sname_si' => 'රජයේ භාෂා පරිවර්තක සේවය',
                'sname_ta' => 'அரசாங்க மொழிபெயர்ப்பாளர் சேவை',
            ],
            [
                'sname_en' => 'Combined Drivers\' Service',
                'sname_si' => 'ඒකාබද්ධ රියැදුරු සේවය',
                'sname_ta' => 'இணைந்த சாரதிகள் சேவை',
            ],
            [
                'sname_en' => 'Office Employees\' Service',
                'sname_si' => 'කාර්යාල සේවක සේවය',
                'sname_ta' => 'அலுவலக ஊழியர் சேவை',
            ],
            [
                'sname_en' => 'Sri Lanka Technological Service',
                'sname_si' => 'ශ්‍රී ලංකා තාක්ෂණ සේවය',
                'sname_ta' => 'இலங்கை தொழிநுட்பவியற் சேவை',
            ],

        ]);
    }
}
