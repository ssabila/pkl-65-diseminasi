@extends('errors::minimal')

@section('title', __('Terlarang'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Terlarang'))
