<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Email Confirmation</h2>
<p>Hi {{ ucwords($name) }},</p>
<p>
    In order to use access your account, you need to verify your email address and activate your account.
    To do this, please click on the following link:
</p>
<p>
    <a href='http://social-lena.dev/check/email/{{ md5($id) }}/{{ $url }}'>http://social-lena.dev/check/email/{{ md5($id) }}/{{$url}}</a>
</p>
<br>
<p>
    Your
    <a href="http://social-lena.dev">social-lena.dev</a>
</p>
</body>
</html>