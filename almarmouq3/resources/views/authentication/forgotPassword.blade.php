<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head />

<body>

    <section class="auth forgot-password-page bg-base d-flex flex-wrap">


        <!-- Left Pane Fill -->
        <div class="auth-left d-lg-block d-none p-0 overflow-hidden">
            <img src="{{ asset('assets/images/auth/forgot-pass-img.png') }}" alt="Auth Background" class="w-100 h-100"
                style="object-fit: cover; object-position: left; display: block;">
        </div>

        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <div>
                    <h4 class="mb-12">Forgot Password</h4>
                    <p class="mb-32 text-secondary-light text-lg">Enter the email address associated with your account
                        and we will send you a link to reset your password.</p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success mb-20">{{ session('status') }}</div>
                @endif
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Enter Email" required
                            autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger-main text-sm mt-12">{{ $message }}</div>
                    @enderror
                    <button type="submit"
                        class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Continue</button>

                    <div class="text-center">
                        <a href="{{ route('signin') }}" class="text-primary-600 fw-bold mt-24">Back to Sign In</a>
                    </div>

                    <div class="mt-120 text-center text-sm">
                        <p class="mb-0">Already have an account? <a href="{{ route('signin') }}"
                                class="text-primary-600 fw-semibold">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

</body>

</html>
