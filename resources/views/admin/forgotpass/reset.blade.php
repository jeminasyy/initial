<x-layout>
    <div style="width: 50%; margin: 1% 5%; display:inline-block; vertical-align: top;">
        <header>
            <h2 class="text-2xl font-bold uppercase mb-1 mb-8">
                Hi {{$user->firstName}}!
            </h2>
            <h2 class="text-2xl font-bold mb-1 mb-8">
                Please create a password
            </h2>
        </header>

        <form method="POST" action="/reset/password/{{$user->id}}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    name="password" 
                    value="{{old('password')}}"
                    id="newpassword"
                />
                @error('password')
                    <p class="text-red-500 text-md mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="confirmation">Confirm Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    name="password_confirmation" 
                    value="{{old('password_confirmation')}}"
                    id="confirmpassword"
                />
                @error('password_confirmation')
                    <p class="text-red-500 text-md mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="mb-6">
                <input type="checkbox" id="checkbox">&nbsp; Show Password
            </div>

            <div class="mb-6">
                <button type="submit" 
                    class="bg-laravel text-white rounded py-2 px-4 hover:bg-black"
                    style="margin-top: 2%; background-color: #EDC304;
                    border: 1px solid#EDC304;
                    border-radius: 5px;"
                >
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</x-layout>