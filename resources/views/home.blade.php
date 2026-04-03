@extends('layouts.app')
@section('title', 'Home')
@section('content')

@include('home.hero-slider-new')

@include('home.categories')

@include('home.new-arrivals')

@include('home.best-selling')

@include('home.festive-banner')

@include('home.mens-collection')

@include('home.ladies-collection')

@include('home.why-us')

@include('home.showroom')

@include('home.delivery-info')

@include('home.newsletter')
@endsection