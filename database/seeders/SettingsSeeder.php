<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            'app_name' => 'Selfie System',
            'footer_text' => '© '.date('Y').' Optimum Code Company',
            'default_language' => 'en',
            'timezone' => 'UTC',
            'email' => 'info@selfieacademy.com',
            'phone' => '+962 790 339 676',
            'logo' => '',
            'dark_logo' => '',
            'favicon' => '',
            'primary_color' => '',
            'secondary_color' => '',
            'tertiary_color' => '',
            'primary_color_button' => '',
            'theme_dark_mode' => '',
            'currency' => 'USD',
            'country' => 'Jordan',
            'openai_key' => '',
            'payment_gateway' => '0',
            'facebook_login' => '0',
            'google_login' => '0',
            'pusher_app_id' => '',
            'pusher_app_key' => '',
            'pusher_app_secret' => '',
            'pusher_app_cluster' => '',
            'seo_author' => '',
            'seo_keywords' => '',
            'seo_description' => '',
            'twilio_sid' => 'AC58e8ecf8e205f42d0f35f5f3d6dbb24b',
            'twilio_auth_token' => '7ccadb908777f00b6cad64e81ee4dc49',
            'twilio_verify_sid' => 'VA46ca026a6d79f36c8b6772e5867390c5',
            'twilio_from' => '+14153221479',
            'mail_host' => 'smtp.gmail.com',
            'mail_from_address' => 'a.w.marashdeh@gmail.com',
            'mail_from_name' => 'Admin',
            'mail_username' => 'a.w.marashdeh@gmail.com',
            'mail_password' => 'fhhulylusgfdgokj',
            'mail_port' => '587',
            'mail_encryption' => 'tls',
        ];

        foreach($defaultSettings as $key => $value){
            Setting::updateOrCreate(['key'=>$key], ['value'=>$value]);
        }
    }
}
