<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Frontweb
$routes->get('/', 'Home::index', ['as' => 'index']);


$routes->post('cart/add', 'CartController::add', ['as' => 'cart.add']);
$routes->get('cart', 'CartController::index', ['as' => 'cart.index']);
$routes->post('cart/update', 'CartController::updateQuantity', ['as' => 'cart.update']);
$routes->post('cart/remove', 'CartController::remove', ['as' => 'cart.remove']);
$routes->get('checkout', 'CartController::checkout', ['as' => 'checkout.index']);
$routes->post('checkout/apply-campaigns','CartController::applyCampaigns',['as' => 'checkout.applyCampaigns']);



// Backoffice
$routes->group('backoffice', function ($routes) {
    $routes->get('/', 'Backoffice::index', ['as' => 'backoffice.index']);


    $routes->get('products', 'Backoffice::products', ['as' => 'backofficeproducts.products']);
    $routes->post('get_products', 'Backoffice::get_products');
    $routes->post('add_product', 'Backoffice::add_product');
    $routes->get('get_product/(:num)', 'Backoffice::get_product/$1');
    $routes->post('update_product', 'Backoffice::update_product');
    $routes->post('delete_product', 'Backoffice::delete_product');


    $routes->get('category', 'Backoffice::category', ['as' => 'backofficeproducts.category']);
    $routes->post('get_category', 'Backoffice::get_category');
    $routes->post('add_category', 'Backoffice::add_category');
    $routes->post('update_category', 'Backoffice::update_category');
    $routes->post('delete_category', 'Backoffice::delete_category');
    $routes->get('dropdown_get_category', 'Backoffice::dropdown_get_category');

    $routes->get('campaign', 'Campaign::index', ['as' => 'backoffice.campaign']);
    $routes->post('get_campaign', 'Campaign::get_campaign');
    $routes->post('add_campaign', 'Campaign::add_campaign');
    $routes->post('delete_campaign', 'Campaign::delete_campaign');
});
