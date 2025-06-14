<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #0056b3;
            margin: 0;
        }
        .content {
            padding: 20px 0;
            text-align: right; /* Align text to the right for Arabic */
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff !important;
            padding: 10px 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 0.9em;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>تأكيد بريدك الإلكتروني</h1>
        </div>
        <div class="content">
            <p>مرحباً {{ $user->first_name }} {{ $user->last_name }}،</p>
            <p>شكراً لتسجيلك في منصتنا. يرجى الضغط على الزر أدناه لتفعيل بريدك الإلكتروني:</p>
            <p style="text-align: center;">
                <a href="{{ $verifyUrl }}" class="button">تفعيل حسابي</a>
            </p>
            <p>إذا كنت تواجه مشكلة في الضغط على الزر، يمكنك نسخ الرابط التالي ولصقه في متصفحك:</p>
            <p><a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></p>
            <p>شكراً لاستخدامك منصتنا!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PALJob . جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
