@extends('layouts.main')

@php
   $model = [
      'email' => 'danilo@bautista',
      'password' => 'tester',
      'phone' => '',
      'language' => 'AR',
   ];

   $countries = [
      [
         'label' => 'Argentina',
         'value' => 'AR'
      ],
      [
         'label' => 'Italy',
         'value' => 'IT'
      ],
      [
         'label' => 'Brazil',
         'value' => 'BR'
      ],
      [
         'label' => 'United States',
         'value' => 'US'
      ],
   ];
@endphp

@section('content')
   <x-form method="POST" operation-type="login">
      <x-form.input name="email" type="email" label="Email" required></x-form.input>
      <x-form.input name="password" id="password" type="password" label="Password" required></x-form.input>
      <x-form.input name="phone" data-onlynumbers type="tel" label="Phone" required></x-form.input>
      <x-form.input name="quantity" type="number" label="Quantity" min="10" required></x-form.input>
      <x-form.select name="language" label="Country" required :options="$countries" :model="$model"></x-form.select>
      <x-button type="button" id="example-button" label="Next" format="inline"></x-button>
      <x-button type="button" id="example-button" label="Next" format="block"></x-button>
      <x-button url="google.com" id="example-button" label="Next" format="clean"></x-button>
      <x-form.textarea name="bio" id="bio" type="editor" label="Bio"></x-form.textarea>
      {{-- <x-video-iframe iframe='<iframe width="490" height="871" src="https://www.youtube.com/embed/ATUYOzVX2os" title="Que le pasa a este señor? 🫣 #artificialintelligence #codificacion #ai" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'></x-video-iframe>
      <x-video-iframe iframe='<iframe width="560" height="315" src="https://www.youtube.com/embed/LDx5Mt87vi4?si=8eLLp9mXZlOh-Pjd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'></x-video-iframe> --}}
   </x-form>
@endsection