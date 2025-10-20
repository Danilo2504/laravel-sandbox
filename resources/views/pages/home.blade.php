@extends('layouts.main')

@section('content')
   <x-form method="POST" type="login">
      <x-form.input name="email" type="email" label="Email" required></x-form.input>
      <x-form.input name="password" type="password" label="Password" required></x-form.input>
      <x-form.input name="phone" type="tel" label="Phone" required></x-form.input>
      <x-form.input name="quantity" type="number" label="Quantity" min="10" required></x-form.input>
   </x-form>
@endsection