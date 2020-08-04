@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white">
                <div class="card-header">users</div>

                <div class="card-body">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th scope="col">@sortablelink('id')</th>
                                <th scope="col">@sortablelink('name')</th>
                                <th scope="col">@sortablelink('email')</th>
                                <th scope="col">Role(s)</th>
                                @if(Auth::user()->can('edit-users') and Auth::user()->can('delete-users'))
                                <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <th scope="row">{{ $user->id}}</th>
                                <td>{{$user ->name}}</td>
                                <td>{{$user ->email}}</td>
                                <td>{{ implode(', ' ,$user ->roles()->get()->pluck('name')->toArray()) }}
                                    @can('edit-users')
                                <td>
                                    <a href="{{route('admin.users.edit', $user->id)}}"><button type="button"
                                            class="btn btn-primary float-left ">Edit</button></a>
                                    @endcan
                                    @can('delete-users')
                                    <form action="{{route('admin.users.destroy', $user)}}" method="POST"
                                        class="float-left" name="delete_user">
                                        @csrf
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $users->appends(\Request::except('page'))->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection