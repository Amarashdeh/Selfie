@extends('admin.layouts.master')
@section('title','Settings')

@section('content')
<div class="container-fluid">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#general">General</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#social">Social Login</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#livechat">Live Chat</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seo">SEO</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#twilio">Twilio</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#email">Email</a></li>
    </ul>

    <div class="tab-content mt-3">

        {{-- General Settings --}}
        <div class="tab-pane fade show active" id="general">
            <form id="form-general" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6"><label>Application Name</label>
                        <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? '' }}"></div>

                    <div class="col-md-6"><label>Footer Text</label>
                        <input type="text" name="footer_text" class="form-control" value="{{ $settings['footer_text'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Application Details</label>
                        <textarea name="application_details" class="form-control">{{ $settings['application_details'] ?? '' }}</textarea></div>

                    <div class="col-md-6 mt-2"><label>Application Map Address</label>
                        <input type="text" name="application_map" class="form-control" value="{{ $settings['application_map'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Default Language</label>
                        <input type="text" name="default_language" class="form-control" value="{{ $settings['default_language'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Timezone</label>
                        <select name="timezone" class="form-control">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ ($settings['timezone'] ?? '') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-2"><label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $settings['email'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $settings['phone'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Office Address</label>
                        <input type="text" name="office_address" class="form-control" value="{{ $settings['office_address'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Office Hours</label>
                        <input type="text" name="office_hours" class="form-control" value="{{ $settings['office_hours'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>OpenAI Key</label>
                        <input type="text" name="openai_key" class="form-control" value="{{ $settings['openai_key'] ?? '' }}"></div>

                    <div class="col-md-6 mt-2"><label>Payment Gateway</label>
                        <select name="payment_gateway" class="form-control">
                            <option value="on" {{ ($settings['payment_gateway'] ?? '')=='on' ? 'selected':'' }}>ON</option>
                            <option value="off" {{ ($settings['payment_gateway'] ?? '')=='off' ? 'selected':'' }}>OFF</option>
                        </select></div>

                    <div class="col-md-4 mt-2"><label>Logo</label>
                        <input type="file" name="logo" class="form-control">
                        @if(!empty($settings['logo']))<img src="{{ asset($settings['logo']) }}" width="80"  data-preview="logo">@endif
                    </div>

                    <div class="col-md-4 mt-2"><label>Dark Logo</label>
                        <input type="file" name="dark_logo" class="form-control">
                        @if(!empty($settings['dark_logo']))<img src="{{ asset($settings['dark_logo']) }}" width="80" data-preview="dark_logo">@endif
                    </div>

                    <div class="col-md-4 mt-2"><label>Favicon</label>
                        <input type="file" name="favicon" class="form-control">
                        @if(!empty($settings['favicon']))<img src="{{ asset($settings['favicon']) }}" width="32" data-preview="favicon">@endif
                    </div>

                    {{-- Theme Colors --}}
                    <div class="col-md-3 mt-2"><label>Primary Color</label>
                        <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#000000' }}" class="form-control"></div>

                    <div class="col-md-3 mt-2"><label>Secondary Color</label>
                        <input type="color" name="secondary_color" value="{{ $settings['secondary_color'] ?? '#000000' }}" class="form-control"></div>

                    <div class="col-md-3 mt-2"><label>Tertiary Color</label>
                        <input type="color" name="tertiary_color" value="{{ $settings['tertiary_color'] ?? '#000000' }}" class="form-control"></div>

                    <div class="col-md-3 mt-2"><label>Primary Button Color</label>
                        <input type="color" name="primary_color_button" value="{{ $settings['primary_color_button'] ?? '#000000' }}" class="form-control"></div>

                    <div class="col-md-3 mt-2"><label>Theme Dark Mode</label>
                        <select name="theme_dark_mode" class="form-control">
                            <option value="on" {{ ($settings['theme_dark_mode'] ?? '')=='on' ? 'selected':'' }}>ON</option>
                            <option value="off" {{ ($settings['theme_dark_mode'] ?? '')=='off' ? 'selected':'' }}>OFF</option>
                        </select></div>

                    <div class="col-md-3 mt-2"><label>Currency</label>
                        <input type="text" name="currency" class="form-control" value="{{ $settings['currency'] ?? '' }}"></div>

                    <div class="col-md-3 mt-2"><label>Country</label>
                        <input type="text" name="country" class="form-control" value="{{ $settings['country'] ?? '' }}"></div>
                </div>

                <button class="btn btn-success mt-3" type="submit">Save General Settings</button>
            </form>
        </div>

        {{-- Social Login --}}
        <div class="tab-pane fade" id="social">
            <form id="form-social">@csrf
                <div class="row mt-2">
                    <div class="col-md-6"><label>Facebook Login</label>
                        <select name="facebook_login" class="form-control">
                            <option value="on" {{ ($settings['facebook_login'] ?? '')=='on' ? 'selected':'' }}>ON</option>
                            <option value="off" {{ ($settings['facebook_login'] ?? '')=='off' ? 'selected':'' }}>OFF</option>
                        </select></div>

                    <div class="col-md-6"><label>Google Login</label>
                        <select name="google_login" class="form-control">
                            <option value="on" {{ ($settings['google_login'] ?? '')=='on' ? 'selected':'' }}>ON</option>
                            <option value="off" {{ ($settings['google_login'] ?? '')=='off' ? 'selected':'' }}>OFF</option>
                        </select></div>
                </div>
                <button class="btn btn-success mt-3" type="submit">Save Social Login</button>
            </form>
        </div>

        {{-- Live Chat --}}
        <div class="tab-pane fade" id="livechat">
            <form id="form-livechat">@csrf
                <div class="row mt-2">
                    @foreach(['pusher_app_id','pusher_app_key','pusher_app_secret','pusher_app_cluster'] as $key)
                        <div class="col-md-6 mt-2">
                            <label>{{ ucwords(str_replace('_',' ',$key)) }}</label>
                            <input type="text" name="{{ $key }}" class="form-control" value="{{ $settings[$key] ?? '' }}">
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-success mt-3" type="submit">Save Live Chat</button>
            </form>
        </div>

        {{-- SEO --}}
        <div class="tab-pane fade" id="seo">
            <form id="form-seo">@csrf
                <div class="row mt-2">
                    <div class="col-md-4"><label>Author</label>
                        <input type="text" name="seo_author" class="form-control" value="{{ $settings['seo_author'] ?? '' }}"></div>
                    <div class="col-md-4"><label>Meta Keywords</label>
                        <input type="text" name="seo_keywords" class="form-control" value="{{ $settings['seo_keywords'] ?? '' }}"></div>
                    <div class="col-md-4"><label>Meta Description</label>
                        <input type="text" name="seo_description" class="form-control" value="{{ $settings['seo_description'] ?? '' }}"></div>
                </div>
                <button class="btn btn-success mt-3" type="submit">Save SEO</button>
            </form>
        </div>

        {{-- Twilio --}}
        <div class="tab-pane fade" id="twilio">
            <form id="form-twilio">@csrf
                <div class="row mt-2">
                    @foreach(['twilio_sid','twilio_auth_token','twilio_verify_sid','twilio_from'] as $key)
                        <div class="col-md-4 mt-2">
                            <label>{{ ucwords(str_replace('_',' ',$key)) }}</label>
                            <input type="text" name="{{ $key }}" class="form-control" value="{{ $settings[$key] ?? '' }}">
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-success mt-3" type="submit">Save Twilio</button>
            </form>
        </div>

        {{-- Email --}}
        <div class="tab-pane fade" id="email">
            <form id="form-email">@csrf
                <div class="row mt-2">
                    @foreach(['mail_host','mail_from_address','mail_from_name','mail_username','mail_password','mail_port','mail_encryption'] as $key)
                        <div class="col-md-3 mt-2">
                            <label>{{ ucwords(str_replace('_',' ',$key)) }}</label>
                            <input type="text" name="{{ $key }}" class="form-control" value="{{ $settings[$key] ?? '' }}">
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-success mt-3" type="submit">Save Email</button>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
function setupAjaxForm(formId, route){
    $(formId).on('submit', function(e){
        e.preventDefault();
        let $form = $(this);
        let formData = new FormData(this);

        $.ajax({
            url: route,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){ 
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                // $.post('/admin/settings/refresh-config', function () {
                // });

                // Update text/number/select/textarea fields
                $form.find('input, select, textarea').each(function(){
                    let name = $(this).attr('name');
                    if(formData.has(name)){
                        let value = formData.get(name);
                        if($(this).is('[type="file"]')) return; // skip file inputs
                        $(this).val(value);
                    }
                });

                // Update file previews
                $form.find('input[type="file"]').each(function(){
                    let name = $(this).attr('name');
                    let file = formData.get(name);
                    if(file && file.name){
                        let reader = new FileReader();
                        reader.onload = function(e){
                            let preview = $form.find('img[data-preview="'+name+'"]');
                            if(preview.length){
                                preview.attr('src', e.target.result);
                            } else {
                                // create a preview if not exists
                                $('<img>').attr({
                                    src: e.target.result,
                                    width: name === 'favicon' ? 32 : 80,
                                    'data-preview': name
                                }).insertAfter($form.find('input[name="'+name+'"]'));
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            },
            error: function(xhr){ 
                Swal.fire('Error', xhr.responseJSON?.message ?? 'Server error','error'); 
            }
        });
    });
}

// Initialize all forms
setupAjaxForm('#form-general','{{ route("admin.settings.general") }}');
setupAjaxForm('#form-social','{{ route("admin.settings.social") }}');
setupAjaxForm('#form-livechat','{{ route("admin.settings.livechat") }}');
setupAjaxForm('#form-seo','{{ route("admin.settings.seo") }}');
setupAjaxForm('#form-twilio','{{ route("admin.settings.twilio") }}');
setupAjaxForm('#form-email','{{ route("admin.settings.email") }}');
</script>
@endpush
