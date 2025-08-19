<!DOCTYPE html>
<html>
<head>
    <title>Verify Your New Email</title>
</head>
<body>
    <h3>Hello {{ $user->name }}</h3>
    <p>You requested to change your email. Please click the link below to verify and update your email:</p>
    <a href="{{ route('admin.profile.verifyNewEmail', ['id'=>$user->id, 'hash'=>$user->email_verification_token]) }}">
        Verify My Email
    </a>
    <p>If you did not request this change, ignore this email.</p>
</body>
</html>