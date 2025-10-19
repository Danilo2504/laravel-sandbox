@extends('layouts.main')

@section('content')
   <x-form method="POST" type="login">
      <x-form.input name="email" type="email" label="Email" required></x-form.input>
      <x-form.input name="password" type="password" label="Password" required></x-form.input>
   </x-form>
@endsection