@extends('layouts.main')

@php
   $model = [
      'email' => 'danilo@bautista',
      'password' => 'tester',
      'phone' => ''
   ]
@endphp

@section('content')
   <x-form method="POST" type="login" :model="$model">
      <x-form.input name="email" type="email" label="Email" required></x-form.input>
      <x-form.input name="password" type="password" label="Password" required></x-form.input>
      <x-form.input name="phone" type="tel" label="Phone" required></x-form.input>
      <x-form.input name="quantity" type="number" label="Quantity" min="10" required></x-form.input>
      <x-form.select name="language" label="Quantity" required></x-form.select>
      <x-form.button type="button" id="example-button" label="Next" format="inline"></x-form.button>
      <x-form.button type="button" id="example-button" label="Next" format="block"></x-form.button>
      <x-form.button url="google.com" id="example-button" label="Next" format="clean"></x-form.button>
   </x-form>
@endsection