<x-layout>
    <x-sidenav>
        @include('partials._settings')
        <div style="width: 75%; display:inline-block; vertical-align: top;">
            <div class="settings-content-container">
                <div class="newCat">
                   <!-- <form>
                        <p>New Category</p>
                        <label>Category name</label>
                        <input type="text" placeholder="Name">
                        <label>Ticket Type</label>
                        <select></select>
                    </form>-->
                </div>

                <div class="table-holder-categories">

                <div class="ticketNav" style="margin-bottom: 5px">
                    <a href="/users">Users</a>
                    <a href="/users/archived" class="active">Archive</a>
                </div>

                @include('partials._search-user')
                <br><br>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>FIRST NAME</th>
                        <th>LAST NAME</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>VERIFIED</th>
                        <th></th>
                    </tr>
        
                    @unless(count($users) == 0)

                    @foreach($users as $user)
                    <tr>
                        <td style="text-align:center">{{$user['id']}}</td>
                        <td>{{$user['firstName']}}</th>
                        <td>{{$user['lastName']}}</td>
                        <td>{{$user['email']}}</td>
                        <td>{{$user['role']}}</td>
                        @if($user['email_verified_at'])
                        <td>
                            {{$user['email_verified_at']->toDateString()}}
                        </td>
                        @else
                        <td></td>
                        @endif
                        <td>
                            <a href="/users/restore/{{$user->id}}"><i class='bx bxs-archive-out'></i>Archive Out</button>
                        </td>
                    </tr>

                    @endforeach

                    @else 
                        <p>No Users Found</p>
                    @endunless


                </table>

                
            </div>
            <div class="paginate" style="float:left">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
        
    </x-sidenav>
</x-layout>