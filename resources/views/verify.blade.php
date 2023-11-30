<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Email Verification</h2>
            <p class="card-text">
                Thank you for registering! To complete the registration process, please use the following verification code:
            </p>
            <p class="card-text">
                Verification Code: <strong>{{ $user->token }}</strong>
            </p>
            <p class="card-text">
                If you did not create an account, no further action is required.
            </p>
        </div>
    </div>

</body>
</html>
