<x-layout>
    <div style="width: 50%; margin: 1% 5%; display:inline-block; vertical-align: top;">
        <header>
            <h2 class="text-2xl font-bold uppercase mb-1 mb-8">
                Hi {{$ticket->student->FName}}!
            </h2>
            <h2 class="text-2xl font-bold mb-1 mb-8">
                Please Provide your feedback.
            </h2>
        </header>

        <form method="POST" action="/{{$ticket->id}}/ticket/setResolved" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label for="role">Rating</label>
                <select 
                    class="form-control"
                    name="rating"
                    value="{{old('rating')}}"
                >
                    <option value="" selected>Choose...</option>
                    <option value=1>1</option>
                    <option value=2>2</option>
                    <option value=3>3</option>
                    <option value=4>4</option>
                    <option value=4>5</option>
                </select>
                @error('rating')
                <p class="text-red-500 text-md mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="comments">Comments:</label>
                <textarea 
                    name="comments" 
                    class="form-control" 
                    id="exampleFormControlTextarea1" 
                    rows="10"
                    value="{{old('comments')}}"
                ></textarea>
            </div>

            <div class="mb-6">
                <button 
                    type="submit" 
                    class="bg-laravel text-white rounded py-2 px-4 hover:bg-black"
                    style="margin-top: 4%; margin-left:2%; background-color: #EDC304;
                    border: 1px solid#EDC304;
                    border-radius: 5px;"
                >
                    Confirm
                </button>
            </div>
        </form>
    </div>
</x-layout>
