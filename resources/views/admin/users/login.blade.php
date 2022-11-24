{{-- <x-layout>
    <form class="form-inline" method="POST" action="users/authenticate">
        @csrf
        <h1 style="color:black; font-family: 'Cabin', sans-serif; font-size: 50px;text-align: center; margin-top: 8%; 
        position: relative; font-weight: 700px;">R.I.C.K Ticketing Website</h1>

        <input 
            type="text" 
            id="email" 
            placeholder=" email address" 
            name="email" 
            style="margin-top: 4%; align-content: center;"
            value="{{old('email')}}"
        >
        <br>
        <input 
            type="password" 
            id="pwd" 
            placeholder="password" 
            name="password" 
            style="margin-top: 2%; align-content: center;"
            value="{{old('password')}}"
        >

        <button type="submit" style="margin-top: 9%;">Log in</button>
        <h3 style="font-family: 'Roboto', sans-serif;font-size: 20; font-weight: bold; margin-left:35.6%;
        margin-top: 7%; text-decoration: underline; color: #454545;">
            Forgot Password
        </h3>
    </form>
</x-layout> --}}


<x-layout>
    <div style="width: 30%; margin: 5% 35%; display:inline-block; vertical-align: top;">
        <header>
            <h2 class="text-4xl font-bold uppercase mb-1 mb-8" style="text-align: center;">
                Log into your account
            </h2>
        </header>

        <form method="POST" action="/users/authenticate" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label for="email">Email</label>
                <input 
                    type="text"
                    style="align-content: center;" 
                    class="form-control" 
                    name="email" 
                    value="{{old('email')}}"
                />
                @error('email')
                <p class="text-red-500 text-md mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    style="align-content: center;"
                    class="form-control" 
                    name="password" 
                    value="{{old('password')}}"
                />
                @error('password')
                <p class="text-red-500 text-md mt-1">{{$message}}</p>
                @enderror
            </div>
            <div class="mb-6">
                <a href="/forgotpassword/verify">Forgot Password?</a>
            </div>

             <div class="mb-6">
                <button type="submit" 
                    class="bg-laravel text-white rounded py-2 px-4 hover:bg-black"
                    style="margin-top: 3%; margin-left:0.8%; background-color: #EDC304;
                    border: 1px solid#EDC304;
                    border-radius: 10px; width:100%"
                >
                    Sign In
                </button>

                {{-- <a href="/users" class="text-black ml-4"> Back </a> --}}
            </div>
        </form>
    </div>
</x-layout>