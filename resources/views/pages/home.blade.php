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
   <div class="container my-5">
      <div class="row">
         <div class="col-12">
            <x-form method="POST" operation-type="login">
               <div class="row g-4">
                  <div class="col-6">
                     <x-form.input name="email" type="email" label="Email" required></x-form.input>
                  </div>
                  <div class="col-6">
                     <x-form.input name="password" id="password" type="password" label="Password" required></x-form.input>
                  </div>
                  <div class="col-6">
                     <x-form.input name="phone" data-numbersonly type="tel" label="Phone" required></x-form.input>
                  </div>
                  <div class="col-6">
                     <x-form.input name="quantity" type="number" label="Quantity" min="10" required></x-form.input>
                  </div>
                  <div class="col-12">
                     <x-form.select name="language" label="Country" required :options="$countries" :model="$model"></x-form.select>
                  </div>
                  <div class="col-6">
                     <x-button type="button" id="button-inline" label="Button Inline"></x-button>
                  </div>
                  <div class="col-6">
                     <x-button url="https://www.google.com" id="link-button" label="Link" format="block"></x-button>
                  </div>
                  <div class="col-12">
                     <x-button type="button" id="modal-trigger" label="Button Block" format="block"></x-button>
                  </div>
                  <div class="col-12">
                     <x-form.textarea name="bio" id="bio" type="editor" label="Bio"></x-form.textarea>
                  </div>
                  <div class="col-12">
                     <x-form.textarea name="comments" id="comments" label="Comments"></x-form.textarea>
                  </div>
                  <div class="col-12">
                     <x-video-iframe iframe='<iframe width="490" height="871" src="https://www.youtube.com/embed/ATUYOzVX2os" title="Que le pasa a este señor? 🫣 #artificialintelligence #codificacion #ai" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'></x-video-iframe>
                  </div>
                  <div class="col-12">
                     <x-video-iframe iframe='<iframe width="560" height="315" src="https://www.youtube.com/embed/LDx5Mt87vi4?si=8eLLp9mXZlOh-Pjd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'></x-video-iframe>
                  </div>
               </div>
            </x-form>
         </div>
      </div>
   </div>
   <div id="modal-loco">
      <h1>Hola mundo</h1>
   </div>
@endsection

@push('scripts')
   <script>
      $(function(){
         // console.log('stardust => ', window?.Stardust);
         $('#modal-trigger').on('click', function(){
            const alert = window.Stardust.register({
               name: 'bootboxAlert',
               element: '#modal-loco',
               options:{
                  message: 'aa'
               }
            });

            window.Stardust.init(alert.id);
         })
      });
   </script>
@endpush