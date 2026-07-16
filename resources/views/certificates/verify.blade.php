<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Verify Certificate | EventSphere</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-4">
                <h1 class="fw-bold">EventSphere</h1>

                <p class="text-muted">
                    Certificate Verification Portal
                </p>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <form method="POST"
                          action="{{ route(
                              'certificates.verify'
                          ) }}">
                        @csrf

                        <label class="form-label">
                            Verification Code
                        </label>

                        <div class="input-group">
                            <input name="verification_code"
                                   class="form-control"
                                   value="{{ old(
                                       'verification_code'
                                   ) }}"
                                   required>

                            <button class="btn btn-primary">
                                Verify
                            </button>
                        </div>

                        @error('verification_code')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </form>

                </div>
            </div>

            @if($searched)

                @if($verificationStatus === 'VALID')

                    <div class="alert alert-success text-center">
                        <h4>Valid Certificate</h4>
                        This certificate is authentic.
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">

                            <h2>{{ $certificate->title }}</h2>

                            <p class="text-muted">
                                Presented to
                            </p>

                            <h3 class="text-primary">
                                {{ $certificate->recipient_name }}
                            </h3>

                            <p>
                                {{ $certificate->event_title }}
                            </p>

                            <p>
                                <strong>Certificate:</strong>
                                {{ $certificate->certificate_number }}
                            </p>

                            <p>
                                <strong>Issued:</strong>
                                {{ $certificate->issued_at }}
                            </p>

                        </div>
                    </div>

                @elseif($verificationStatus === 'REVOKED')

                    <div class="alert alert-danger text-center">
                        <h4>Certificate Revoked</h4>
                        This certificate is no longer valid.
                    </div>

                @else

                    <div class="alert alert-warning text-center">
                        <h4>Certificate Not Found</h4>
                        The verification code is invalid.
                    </div>

                @endif

            @endif

        </div>
    </div>

</div>

</body>
</html>