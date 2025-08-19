<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value','key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    private function saveSettings(Request $request, $keys)
    {
        $envUpdates = [];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);

                // Save to DB
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);

                // Queue sensitive keys for .env update
                if (in_array($key, [
                    'openai_key','mail_host','mail_from_address','mail_from_name',
                    'mail_username','mail_password','mail_port','mail_encryption',
                    'pusher_app_id','pusher_app_key','pusher_app_secret','pusher_app_cluster',
                    'twilio_sid','twilio_auth_token','twilio_verify_sid','twilio_from'
                ])) {
                    $envUpdates[strtoupper($key)] = $value;
                }
            }
        }

        // Update .env once
        if (!empty($envUpdates)) {
            $this->updateEnvBatch($envUpdates);
        }

        return response()->json(['message' => 'Settings saved successfully']);
    }

    private function updateEnvBatch(array $updates)
    {
        $path = base_path('.env');
        if (!file_exists($path)) return;

        $content = file_get_contents($path);

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($path, $content);
    }

    public function saveGeneral(Request $request)
    {
        $keys = [
            'app_name','footer_text','application_details','application_map','default_language','timezone',
            'email','phone','office_address','office_hours','payment_gateway','theme_dark_mode',
            'logo','dark_logo','favicon','primary_color','primary_color_rgb','secondary_color',
            'secondary_color_rgb','tertiary_color','tertiary_color_rgb','primary_color_button',
            'currency','country'
        ];

        // Validate timezone
        if($request->has('timezone') && !in_array($request->input('timezone'), timezone_identifiers_list())){
            return response()->json(['message'=>'Invalid timezone'], 422);
        }

        // Handle files
        foreach(['logo','dark_logo','favicon'] as $fileKey){
            if($request->hasFile($fileKey)){
                $file = $request->file($fileKey);
                $filename = $fileKey.'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('settings', $filename);
                $request->merge([$fileKey => 'storage/settings/'.$filename]);
            }
        }

        return $this->saveSettings($request,$keys);
    }

    public function saveSocial(Request $request){
        return $this->saveSettings($request,['facebook_login','google_login']);
    }

    public function saveLiveChat(Request $request){
        return $this->saveSettings($request,['pusher_app_id','pusher_app_key','pusher_app_secret','pusher_app_cluster']);
    }

    public function saveSeo(Request $request){
        return $this->saveSettings($request,['seo_author','seo_keywords','seo_description']);
    }

    public function saveTwilio(Request $request){
        return $this->saveSettings($request,['twilio_sid','twilio_auth_token','twilio_verify_sid', 'twilio_from']);
    }

    public function saveEmail(Request $request){
        return $this->saveSettings($request,['mail_host','mail_from_address','mail_from_name','mail_username','mail_password','mail_port','mail_encryption']);
    }
}
