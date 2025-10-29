<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.1.4/dist/tailwind.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('favicon.png') }}">

    <style>
        body {
            margin: 0;
            box-sizing: border-box;
        }

        .login-container {
            background: #f4f5f7;
            min-height: 100vh;
            margin: auto;
            /* border: 1px solid black; */
            display: flex;
            justify-content: center;
            /* align-items: center; */

        }

        .login-box {
            background: white;
            border-radius: 10px;
            padding: 40px;
            /* border: 1px solid red; */
            height: 600px;
            width: 650px;
            margin: auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);


        }

        .btn-primary {
            background-color: #2A3285;
            border-color: #2A3285;
        }

        .btn-primary:hover {
            background-color: #1D256A;
            border-color: #1D256A;
        }

        .login-logo {
            display: block;
            margin: 0 auto;
            width: 120px;
        }

        .emoji-side {
            font-size: 5rem;
            color: #2A3285;
        }
    </style>
</head>

<body class="font-sans leading-normal tracking-wider">

    <div class="login-container flex justify-center items-center">

        <div class="login-box w-full sm:w-96">
            <div class="text-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="PSASB Logo" class="login-logo mb-4">
                <h2 class="text-3xl font-semibold text-indigo-800">Sign In</h2>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label text-lg font-medium text-indigo-800">Email address</label>
                    <input
                        class="form-control w-full p-3 border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        type="email" name="email" id="email" required placeholder="Enter your email"
                        value="{{ old('email') }}">
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-lg font-medium text-indigo-800">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password"
                            class="form-control w-full p-3 border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter your password">
                        <div class="input-group-text cursor-pointer" data-password="false">
                            <span class="password-eye text-indigo-800">👁️</span>
                        </div>
                    </div>
                    @error('password')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                    <label class="form-check-label text-indigo-800" for="checkbox-signin">Remember me</label>
                </div>
            </div> --}}

                <div class="text-center d-grid mb-4">
                    <button type="submit"
                        class="btn btn-primary text-white w-full py-3 text-lg font-medium rounded-lg">
                        Log In
                    </button>
                </div>


            </form>
        </div>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
