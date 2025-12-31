<!doctype html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>New Account</title>
        <style type="text/css">
            :root {
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;

                --backgroud-color: #f8fafc;
                --text-color: #09090b;
                --container-bg-color: #ffffff;
                --container-border-color: #e6e9ee;
                --heading-color: #fb2c36;
                --credentials-bg-color: #f1f5f9;
                --credentials-border-color: #e2e8f0;
                --strong-color: #09090b;

                --font-family-base: Arial, Helvetica, sans-serif;
            }

            /* Simple CSS2 */
            body {
                background: var(--backgroud-color);
                color: var(--text-color);
                font-family: var(--font-family-base);
                margin: 0;
                padding: 20px;
            }

            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background: var(--container-bg-color);
                border: 1px solid var(--container-border-color);
                padding: 18px;
            }

            h1 {
                color: var(--heading-color);
                font-size: 18px;
                margin: 0 0 12px 0;
            }

            p {
                font-size: 14px;
                line-height: 1.4;
                margin: 8px 0;
            }

            .credentials {
                background: var(--credentials-bg-color);
                border: 1px solid var(--credentials-border-color);
                padding: 10px;
                display: block;
                margin: 10px 0;
            }

            strong {
                color: var(--strong-color);
            }
        </style>
    </head>

    <body>
        @php
            $sanitize = fn($value) => str_replace('&#039;', "'", e($value));
        @endphp
        <div class="container">
            <h1>New account created</h1>

            <p>Hi {!! $sanitize($name) !!},</p>

            <p>An account has been created for you on our system.</p>

            <span class="credentials">
                Email: {!! $sanitize($email) !!}<br>
                Password: <strong>{!! $sanitize($password) !!}</strong>
            </span>

            <p>Please log in and change your password as soon as possible.</p>

            <p>Thanks,<br>
                {!! $sanitize($appName) !!}</p>
        </div>
    </body>

</html>
