<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function orderTransaction(Request $request)
    {
        return $request;
    }

    public function orderTransaction1(Request $request)
    {
        $request = [
            "id" => 1,
            "delivery_status" => "Pending",
            "user_id" => 3,
            "organ_id" => 4,
            "dish_available_id" => 2,
            "transaction_id" => null,
            "delivery_latitude" => null,
            "delivery_longitude" => null,
            "order_description" => null,
            "created_at" => null,
            "updated_at" => "2022-08-04T10:03:03.669663+00:00",
            "dish_available" => null,
            "order_dish" => [
                [
                    "id" => 0,
                    "quantity" => 1,
                    "order_id" => 1,
                    "dish_id" => 1,
                    "dish" => [
                        "id" => 1,
                        "name" => "Nasi Ayam",
                        "price" => 10,
                        "dish_image" =>
                            "https://resepichenom.com/media/895c9dbf91933941985d76d069f9b1982912b5da.jpeg",
                        "created_at" => "2022-07-28T06:52:09+00:00",
                        "updated_at" => "2022-07-28T06:52:10+00:00",
                        "organ_id" => 4,
                        "dish_type" => 9,
                        "dish_available" => [
                            [
                                "id" => 2,
                                "date" => "2022-09-28T00:00:00",
                                "time" => "12:00:00",
                                "latitude" => 2.3138,
                                "longtitude" => 102.3211,
                                "delivery_address" => "Ficts, UTeM, Ayer Keroh",
                                "dish_id" => 1,
                                "dish" => null,
                                "orders" => [
                                    [
                                        "id" => 2,
                                        "delivery_status" => "Soon",
                                        "user_id" => 4,
                                        "organ_id" => 4,
                                        "dish_available_id" => 2,
                                        "transaction_id" => null,
                                        "delivery_latitude" => 2.3138,
                                        "delivery_longitude" => 102.3211,
                                        "order_description" => null,
                                        "created_at" => "2022-07-28T06:59:30+00:00",
                                        "updated_at" => "2022-07-28T06:59:31+00:00",
                                        "dish_available" => null,
                                        "order_dish" => [
                                            [
                                                "id" => 2,
                                                "quantity" => 4,
                                                "order_id" => 2,
                                                "dish_id" => 1,
                                                "dish" => null,
                                                "color" => "#FFFFFF",
                                            ],
                                            [
                                                "id" => 6,
                                                "quantity" => 9,
                                                "order_id" => 2,
                                                "dish_id" => 1,
                                                "dish" => null,
                                                "color" => "#FFFFFF",
                                            ],
                                            [
                                                "id" => 7,
                                                "quantity" => 6,
                                                "order_id" => 2,
                                                "dish_id" => 1,
                                                "dish" => null,
                                                "color" => "#FFFFFF",
                                            ],
                                        ],
                                        "organization" => [
                                            "id" => 4,
                                            "code" => "KM001",
                                            "email" => "admin_makan@gmail.com",
                                            "nama" => "makan-makan",
                                            "telno" => "0149392256",
                                            "address" => "UTeM, Ayer Keroh",
                                            "rating" => "No review yet",
                                            "postcode" => "34400",
                                            "state" => "Melaka",
                                            "fixed_charges" => "2.50",
                                            "remember_token" => null,
                                            "created_at" => null,
                                            "updated_at" => null,
                                            "type_org" => 8,
                                            "deleted_at" => null,
                                            "seller_id" => null,
                                            "district" => "(NULL)",
                                            "description" => "(NULL)",
                                            "city" => null,
                                            "organization_picture" =>
                                                "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                                            "activities" => [],
                                            "class_organization" => [],
                                            "donation_organization" => [],
                                            "fees_new" => [],
                                            "organization_user" => [
                                                [
                                                    "id" => 6,
                                                    "organization_id" => 4,
                                                    "user_id" => 3,
                                                    "role_id" => 2,
                                                    "start_date" => null,
                                                    "end_date" => null,
                                                    "status" => null,
                                                    "fees_status" => null,
                                                    "class_organization" => [],
                                                    "fees_new_organization_user" => [],
                                                    "organization_roles" => [
                                                        "id" => 2,
                                                        "nama" => "Admin",
                                                        "organization_user" => [
                                                            [
                                                                "id" => 1,
                                                                "organization_id" => 1,
                                                                "user_id" => 1,
                                                                "role_id" => 2,
                                                                "start_date" => null,
                                                                "end_date" => null,
                                                                "status" => null,
                                                                "fees_status" => null,
                                                                "class_organization" => [],
                                                                "fees_new_organization_user" => [],
                                                                "organization" => [
                                                                    "id" => 1,
                                                                    "code" => "MS001",
                                                                    "email" =>
                                                                        "admin_masjid@gmail.com",
                                                                    "nama" =>
                                                                        "Masjid Al-Alami",
                                                                    "telno" =>
                                                                        "01139893143",
                                                                    "address" =>
                                                                        "UTeM, Ayer Keroh",
                                                                    "rating" =>
                                                                        "No review yet",
                                                                    "postcode" =>
                                                                        "34400",
                                                                    "state" => "Melaka",
                                                                    "fixed_charges" =>
                                                                        "3.00",
                                                                    "remember_token" => null,
                                                                    "created_at" =>
                                                                        "2020-06-07T02:48:33+00:00",
                                                                    "updated_at" =>
                                                                        "2020-06-07T02:52:01+00:00",
                                                                    "type_org" => 4,
                                                                    "deleted_at" => null,
                                                                    "seller_id" => null,
                                                                    "district" => "",
                                                                    "description" => "",
                                                                    "city" => null,
                                                                    "organization_picture" => null,
                                                                    "activities" => [],
                                                                    "class_organization" => [],
                                                                    "donation_organization" => [
                                                                        [
                                                                            "id" => 1,
                                                                            "donation_id" => 1,
                                                                            "organization_id" => 1,
                                                                            "donation" => [
                                                                                "id" => 1,
                                                                                "nama" =>
                                                                                    "Derma Kilat Pembinaan Tandas",
                                                                                "description" =>
                                                                                    "Ayuh Derma",
                                                                                "imagebyte" => null,
                                                                                "url" =>
                                                                                    "Derma-Kilat-Pembinaan-Tandas",
                                                                                "date_created" =>
                                                                                    "2022-08-26T00:00:00",
                                                                                "date_started" =>
                                                                                    "2022-08-26T00:00:00",
                                                                                "date_end" =>
                                                                                    "2022-08-26T00:00:00",
                                                                                "status" =>
                                                                                    "1",
                                                                                "tax_payer" =>
                                                                                    "UTeM",
                                                                                "total_tax" => 1,
                                                                                "donation_poster" => null,
                                                                                "deleted_at" => null,
                                                                                "donation_type" => 5,
                                                                                "code" => null,
                                                                            ],
                                                                            "organization" => null,
                                                                        ],
                                                                        null,
                                                                    ],
                                                                    "fees_new" => [],
                                                                    "organization_user" => [],
                                                                    "type_organizations" => null,
                                                                ],
                                                                "organization_user_student" => [],
                                                                "user" => null,
                                                            ],
                                                            null,
                                                        ],
                                                    ],
                                                    "organization" => null,
                                                    "organization_user_student" => [],
                                                    "user" => null,
                                                ],
                                            ],
                                            "type_organizations" => null,
                                        ],
                                        "transaction" => null,
                                        "user" => null,
                                    ],
                                ],
                            ],
                        ],
                        "dish_type1" => null,
                        "organization" => [
                            "id" => 4,
                            "code" => "KM001",
                            "email" => "admin_makan@gmail.com",
                            "nama" => "makan-makan",
                            "telno" => "0149392256",
                            "address" => "UTeM, Ayer Keroh",
                            "rating" => "No review yet",
                            "postcode" => "34400",
                            "state" => "Melaka",
                            "fixed_charges" => "2.50",
                            "remember_token" => null,
                            "created_at" => null,
                            "updated_at" => null,
                            "type_org" => 8,
                            "deleted_at" => null,
                            "seller_id" => null,
                            "district" => "(NULL)",
                            "description" => "(NULL)",
                            "city" => null,
                            "organization_picture" =>
                                "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                            "activities" => [],
                            "class_organization" => [],
                            "donation_organization" => [],
                            "fees_new" => [],
                            "organization_user" => [
                                [
                                    "id" => 6,
                                    "organization_id" => 4,
                                    "user_id" => 3,
                                    "role_id" => 2,
                                    "start_date" => null,
                                    "end_date" => null,
                                    "status" => null,
                                    "fees_status" => null,
                                    "class_organization" => [],
                                    "fees_new_organization_user" => [],
                                    "organization_roles" => [
                                        "id" => 2,
                                        "nama" => "Admin",
                                        "organization_user" => [
                                            [
                                                "id" => 1,
                                                "organization_id" => 1,
                                                "user_id" => 1,
                                                "role_id" => 2,
                                                "start_date" => null,
                                                "end_date" => null,
                                                "status" => null,
                                                "fees_status" => null,
                                                "class_organization" => [],
                                                "fees_new_organization_user" => [],
                                                "organization" => [
                                                    "id" => 1,
                                                    "code" => "MS001",
                                                    "email" => "admin_masjid@gmail.com",
                                                    "nama" => "Masjid Al-Alami",
                                                    "telno" => "01139893143",
                                                    "address" => "UTeM, Ayer Keroh",
                                                    "rating" => "No review yet",
                                                    "postcode" => "34400",
                                                    "state" => "Melaka",
                                                    "fixed_charges" => "3.00",
                                                    "remember_token" => null,
                                                    "created_at" =>
                                                        "2020-06-07T02:48:33+00:00",
                                                    "updated_at" =>
                                                        "2020-06-07T02:52:01+00:00",
                                                    "type_org" => 4,
                                                    "deleted_at" => null,
                                                    "seller_id" => null,
                                                    "district" => "",
                                                    "description" => "",
                                                    "city" => null,
                                                    "organization_picture" => null,
                                                    "activities" => [],
                                                    "class_organization" => [],
                                                    "donation_organization" => [
                                                        [
                                                            "id" => 1,
                                                            "donation_id" => 1,
                                                            "organization_id" => 1,
                                                            "donation" => [
                                                                "id" => 1,
                                                                "nama" =>
                                                                    "Derma Kilat Pembinaan Tandas",
                                                                "description" =>
                                                                    "Ayuh Derma",
                                                                "imagebyte" => null,
                                                                "url" =>
                                                                    "Derma-Kilat-Pembinaan-Tandas",
                                                                "date_created" =>
                                                                    "2022-08-26T00:00:00",
                                                                "date_started" =>
                                                                    "2022-08-26T00:00:00",
                                                                "date_end" =>
                                                                    "2022-08-26T00:00:00",
                                                                "status" => "1",
                                                                "tax_payer" => "UTeM",
                                                                "total_tax" => 1,
                                                                "donation_poster" => null,
                                                                "deleted_at" => null,
                                                                "donation_type" => 5,
                                                                "code" => null,
                                                            ],
                                                            "organization" => null,
                                                        ],
                                                        null,
                                                    ],
                                                    "fees_new" => [],
                                                    "organization_user" => [],
                                                    "type_organizations" => null,
                                                ],
                                                "organization_user_student" => [],
                                                "user" => null,
                                            ],
                                            null,
                                        ],
                                    ],
                                    "organization" => null,
                                    "organization_user_student" => [],
                                    "user" => null,
                                ],
                            ],
                            "type_organizations" => null,
                        ],
                        "order_dish" => [
                            [
                                "id" => 2,
                                "quantity" => 4,
                                "order_id" => 2,
                                "dish_id" => 1,
                                "dish" => null,
                                "order" => [
                                    "id" => 2,
                                    "delivery_status" => "Soon",
                                    "user_id" => 4,
                                    "organ_id" => 4,
                                    "dish_available_id" => 2,
                                    "transaction_id" => null,
                                    "delivery_latitude" => 2.3138,
                                    "delivery_longitude" => 102.3211,
                                    "order_description" => null,
                                    "created_at" => "2022-07-28T06:59:30+00:00",
                                    "updated_at" => "2022-07-28T06:59:31+00:00",
                                    "dish_available" => null,
                                    "order_dish" => [
                                        [
                                            "id" => 6,
                                            "quantity" => 9,
                                            "order_id" => 2,
                                            "dish_id" => 1,
                                            "dish" => null,
                                            "color" => "#FFFFFF",
                                        ],
                                        [
                                            "id" => 7,
                                            "quantity" => 6,
                                            "order_id" => 2,
                                            "dish_id" => 1,
                                            "dish" => null,
                                            "color" => "#FFFFFF",
                                        ],
                                    ],
                                    "organization" => [
                                        "id" => 4,
                                        "code" => "KM001",
                                        "email" => "admin_makan@gmail.com",
                                        "nama" => "makan-makan",
                                        "telno" => "0149392256",
                                        "address" => "UTeM, Ayer Keroh",
                                        "rating" => "No review yet",
                                        "postcode" => "34400",
                                        "state" => "Melaka",
                                        "fixed_charges" => "2.50",
                                        "remember_token" => null,
                                        "created_at" => null,
                                        "updated_at" => null,
                                        "type_org" => 8,
                                        "deleted_at" => null,
                                        "seller_id" => null,
                                        "district" => "(NULL)",
                                        "description" => "(NULL)",
                                        "city" => null,
                                        "organization_picture" =>
                                            "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                                        "activities" => [],
                                        "class_organization" => [],
                                        "donation_organization" => [],
                                        "fees_new" => [],
                                        "organization_user" => [
                                            [
                                                "id" => 6,
                                                "organization_id" => 4,
                                                "user_id" => 3,
                                                "role_id" => 2,
                                                "start_date" => null,
                                                "end_date" => null,
                                                "status" => null,
                                                "fees_status" => null,
                                                "class_organization" => [],
                                                "fees_new_organization_user" => [],
                                                "organization_roles" => [
                                                    "id" => 2,
                                                    "nama" => "Admin",
                                                    "organization_user" => [
                                                        [
                                                            "id" => 1,
                                                            "organization_id" => 1,
                                                            "user_id" => 1,
                                                            "role_id" => 2,
                                                            "start_date" => null,
                                                            "end_date" => null,
                                                            "status" => null,
                                                            "fees_status" => null,
                                                            "class_organization" => [],
                                                            "fees_new_organization_user" => [],
                                                            "organization" => [
                                                                "id" => 1,
                                                                "code" => "MS001",
                                                                "email" =>
                                                                    "admin_masjid@gmail.com",
                                                                "nama" =>
                                                                    "Masjid Al-Alami",
                                                                "telno" =>
                                                                    "01139893143",
                                                                "address" =>
                                                                    "UTeM, Ayer Keroh",
                                                                "rating" =>
                                                                    "No review yet",
                                                                "postcode" => "34400",
                                                                "state" => "Melaka",
                                                                "fixed_charges" =>
                                                                    "3.00",
                                                                "remember_token" => null,
                                                                "created_at" =>
                                                                    "2020-06-07T02:48:33+00:00",
                                                                "updated_at" =>
                                                                    "2020-06-07T02:52:01+00:00",
                                                                "type_org" => 4,
                                                                "deleted_at" => null,
                                                                "seller_id" => null,
                                                                "district" => "",
                                                                "description" => "",
                                                                "city" => null,
                                                                "organization_picture" => null,
                                                                "activities" => [],
                                                                "class_organization" => [],
                                                                "donation_organization" => [
                                                                    [
                                                                        "id" => 1,
                                                                        "donation_id" => 1,
                                                                        "organization_id" => 1,
                                                                        "donation" => [
                                                                            "id" => 1,
                                                                            "nama" =>
                                                                                "Derma Kilat Pembinaan Tandas",
                                                                            "description" =>
                                                                                "Ayuh Derma",
                                                                            "imagebyte" => null,
                                                                            "url" =>
                                                                                "Derma-Kilat-Pembinaan-Tandas",
                                                                            "date_created" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "date_started" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "date_end" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "status" =>
                                                                                "1",
                                                                            "tax_payer" =>
                                                                                "UTeM",
                                                                            "total_tax" => 1,
                                                                            "donation_poster" => null,
                                                                            "deleted_at" => null,
                                                                            "donation_type" => 5,
                                                                            "code" => null,
                                                                        ],
                                                                        "organization" => null,
                                                                    ],
                                                                    null,
                                                                ],
                                                                "fees_new" => [],
                                                                "organization_user" => [],
                                                                "type_organizations" => null,
                                                            ],
                                                            "organization_user_student" => [],
                                                            "user" => null,
                                                        ],
                                                        null,
                                                    ],
                                                ],
                                                "organization" => null,
                                                "organization_user_student" => [],
                                                "user" => null,
                                            ],
                                        ],
                                        "type_organizations" => null,
                                    ],
                                    "transaction" => null,
                                    "user" => null,
                                ],
                                "color" => "#FFFFFF",
                            ],
                            [
                                "id" => 6,
                                "quantity" => 9,
                                "order_id" => 2,
                                "dish_id" => 1,
                                "dish" => null,
                                "order" => [
                                    "id" => 2,
                                    "delivery_status" => "Soon",
                                    "user_id" => 4,
                                    "organ_id" => 4,
                                    "dish_available_id" => 2,
                                    "transaction_id" => null,
                                    "delivery_latitude" => 2.3138,
                                    "delivery_longitude" => 102.3211,
                                    "order_description" => null,
                                    "created_at" => "2022-07-28T06:59:30+00:00",
                                    "updated_at" => "2022-07-28T06:59:31+00:00",
                                    "dish_available" => null,
                                    "order_dish" => [
                                        [
                                            "id" => 2,
                                            "quantity" => 4,
                                            "order_id" => 2,
                                            "dish_id" => 1,
                                            "dish" => null,
                                            "color" => "#FFFFFF",
                                        ],
                                        [
                                            "id" => 7,
                                            "quantity" => 6,
                                            "order_id" => 2,
                                            "dish_id" => 1,
                                            "dish" => null,
                                            "color" => "#FFFFFF",
                                        ],
                                    ],
                                    "organization" => [
                                        "id" => 4,
                                        "code" => "KM001",
                                        "email" => "admin_makan@gmail.com",
                                        "nama" => "makan-makan",
                                        "telno" => "0149392256",
                                        "address" => "UTeM, Ayer Keroh",
                                        "rating" => "No review yet",
                                        "postcode" => "34400",
                                        "state" => "Melaka",
                                        "fixed_charges" => "2.50",
                                        "remember_token" => null,
                                        "created_at" => null,
                                        "updated_at" => null,
                                        "type_org" => 8,
                                        "deleted_at" => null,
                                        "seller_id" => null,
                                        "district" => "(NULL)",
                                        "description" => "(NULL)",
                                        "city" => null,
                                        "organization_picture" =>
                                            "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                                        "activities" => [],
                                        "class_organization" => [],
                                        "donation_organization" => [],
                                        "fees_new" => [],
                                        "organization_user" => [
                                            [
                                                "id" => 6,
                                                "organization_id" => 4,
                                                "user_id" => 3,
                                                "role_id" => 2,
                                                "start_date" => null,
                                                "end_date" => null,
                                                "status" => null,
                                                "fees_status" => null,
                                                "class_organization" => [],
                                                "fees_new_organization_user" => [],
                                                "organization_roles" => [
                                                    "id" => 2,
                                                    "nama" => "Admin",
                                                    "organization_user" => [
                                                        [
                                                            "id" => 1,
                                                            "organization_id" => 1,
                                                            "user_id" => 1,
                                                            "role_id" => 2,
                                                            "start_date" => null,
                                                            "end_date" => null,
                                                            "status" => null,
                                                            "fees_status" => null,
                                                            "class_organization" => [],
                                                            "fees_new_organization_user" => [],
                                                            "organization" => [
                                                                "id" => 1,
                                                                "code" => "MS001",
                                                                "email" =>
                                                                    "admin_masjid@gmail.com",
                                                                "nama" =>
                                                                    "Masjid Al-Alami",
                                                                "telno" =>
                                                                    "01139893143",
                                                                "address" =>
                                                                    "UTeM, Ayer Keroh",
                                                                "rating" =>
                                                                    "No review yet",
                                                                "postcode" => "34400",
                                                                "state" => "Melaka",
                                                                "fixed_charges" =>
                                                                    "3.00",
                                                                "remember_token" => null,
                                                                "created_at" =>
                                                                    "2020-06-07T02:48:33+00:00",
                                                                "updated_at" =>
                                                                    "2020-06-07T02:52:01+00:00",
                                                                "type_org" => 4,
                                                                "deleted_at" => null,
                                                                "seller_id" => null,
                                                                "district" => "",
                                                                "description" => "",
                                                                "city" => null,
                                                                "organization_picture" => null,
                                                                "activities" => [],
                                                                "class_organization" => [],
                                                                "donation_organization" => [
                                                                    [
                                                                        "id" => 1,
                                                                        "donation_id" => 1,
                                                                        "organization_id" => 1,
                                                                        "donation" => [
                                                                            "id" => 1,
                                                                            "nama" =>
                                                                                "Derma Kilat Pembinaan Tandas",
                                                                            "description" =>
                                                                                "Ayuh Derma",
                                                                            "imagebyte" => null,
                                                                            "url" =>
                                                                                "Derma-Kilat-Pembinaan-Tandas",
                                                                            "date_created" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "date_started" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "date_end" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "status" =>
                                                                                "1",
                                                                            "tax_payer" =>
                                                                                "UTeM",
                                                                            "total_tax" => 1,
                                                                            "donation_poster" => null,
                                                                            "deleted_at" => null,
                                                                            "donation_type" => 5,
                                                                            "code" => null,
                                                                        ],
                                                                        "organization" => null,
                                                                    ],
                                                                    null,
                                                                ],
                                                                "fees_new" => [],
                                                                "organization_user" => [],
                                                                "type_organizations" => null,
                                                            ],
                                                            "organization_user_student" => [],
                                                            "user" => null,
                                                        ],
                                                        null,
                                                    ],
                                                ],
                                                "organization" => null,
                                                "organization_user_student" => [],
                                                "user" => null,
                                            ],
                                        ],
                                        "type_organizations" => null,
                                    ],
                                    "transaction" => null,
                                    "user" => null,
                                ],
                                "color" => "#FFFFFF",
                            ],
                            [
                                "id" => 7,
                                "quantity" => 6,
                                "order_id" => 2,
                                "dish_id" => 1,
                                "dish" => null,
                                "order" => [
                                    "id" => 2,
                                    "delivery_status" => "Soon",
                                    "user_id" => 4,
                                    "organ_id" => 4,
                                    "dish_available_id" => 2,
                                    "transaction_id" => null,
                                    "delivery_latitude" => 2.3138,
                                    "delivery_longitude" => 102.3211,
                                    "order_description" => null,
                                    "created_at" => "2022-07-28T06:59:30+00:00",
                                    "updated_at" => "2022-07-28T06:59:31+00:00",
                                    "dish_available" => null,
                                    "order_dish" => [
                                        [
                                            "id" => 2,
                                            "quantity" => 4,
                                            "order_id" => 2,
                                            "dish_id" => 1,
                                            "dish" => null,
                                            "color" => "#FFFFFF",
                                        ],
                                        [
                                            "id" => 6,
                                            "quantity" => 9,
                                            "order_id" => 2,
                                            "dish_id" => 1,
                                            "dish" => null,
                                            "color" => "#FFFFFF",
                                        ],
                                    ],
                                    "organization" => [
                                        "id" => 4,
                                        "code" => "KM001",
                                        "email" => "admin_makan@gmail.com",
                                        "nama" => "makan-makan",
                                        "telno" => "0149392256",
                                        "address" => "UTeM, Ayer Keroh",
                                        "rating" => "No review yet",
                                        "postcode" => "34400",
                                        "state" => "Melaka",
                                        "fixed_charges" => "2.50",
                                        "remember_token" => null,
                                        "created_at" => null,
                                        "updated_at" => null,
                                        "type_org" => 8,
                                        "deleted_at" => null,
                                        "seller_id" => null,
                                        "district" => "(NULL)",
                                        "description" => "(NULL)",
                                        "city" => null,
                                        "organization_picture" =>
                                            "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                                        "activities" => [],
                                        "class_organization" => [],
                                        "donation_organization" => [],
                                        "fees_new" => [],
                                        "organization_user" => [
                                            [
                                                "id" => 6,
                                                "organization_id" => 4,
                                                "user_id" => 3,
                                                "role_id" => 2,
                                                "start_date" => null,
                                                "end_date" => null,
                                                "status" => null,
                                                "fees_status" => null,
                                                "class_organization" => [],
                                                "fees_new_organization_user" => [],
                                                "organization_roles" => [
                                                    "id" => 2,
                                                    "nama" => "Admin",
                                                    "organization_user" => [
                                                        [
                                                            "id" => 1,
                                                            "organization_id" => 1,
                                                            "user_id" => 1,
                                                            "role_id" => 2,
                                                            "start_date" => null,
                                                            "end_date" => null,
                                                            "status" => null,
                                                            "fees_status" => null,
                                                            "class_organization" => [],
                                                            "fees_new_organization_user" => [],
                                                            "organization" => [
                                                                "id" => 1,
                                                                "code" => "MS001",
                                                                "email" =>
                                                                    "admin_masjid@gmail.com",
                                                                "nama" =>
                                                                    "Masjid Al-Alami",
                                                                "telno" =>
                                                                    "01139893143",
                                                                "address" =>
                                                                    "UTeM, Ayer Keroh",
                                                                "rating" =>
                                                                    "No review yet",
                                                                "postcode" => "34400",
                                                                "state" => "Melaka",
                                                                "fixed_charges" =>
                                                                    "3.00",
                                                                "remember_token" => null,
                                                                "created_at" =>
                                                                    "2020-06-07T02:48:33+00:00",
                                                                "updated_at" =>
                                                                    "2020-06-07T02:52:01+00:00",
                                                                "type_org" => 4,
                                                                "deleted_at" => null,
                                                                "seller_id" => null,
                                                                "district" => "",
                                                                "description" => "",
                                                                "city" => null,
                                                                "organization_picture" => null,
                                                                "activities" => [],
                                                                "class_organization" => [],
                                                                "donation_organization" => [
                                                                    [
                                                                        "id" => 1,
                                                                        "donation_id" => 1,
                                                                        "organization_id" => 1,
                                                                        "donation" => [
                                                                            "id" => 1,
                                                                            "nama" =>
                                                                                "Derma Kilat Pembinaan Tandas",
                                                                            "description" =>
                                                                                "Ayuh Derma",
                                                                            "imagebyte" => null,
                                                                            "url" =>
                                                                                "Derma-Kilat-Pembinaan-Tandas",
                                                                            "date_created" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "date_started" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "date_end" =>
                                                                                "2022-08-26T00:00:00",
                                                                            "status" =>
                                                                                "1",
                                                                            "tax_payer" =>
                                                                                "UTeM",
                                                                            "total_tax" => 1,
                                                                            "donation_poster" => null,
                                                                            "deleted_at" => null,
                                                                            "donation_type" => 5,
                                                                            "code" => null,
                                                                        ],
                                                                        "organization" => null,
                                                                    ],
                                                                    null,
                                                                ],
                                                                "fees_new" => [],
                                                                "organization_user" => [],
                                                                "type_organizations" => null,
                                                            ],
                                                            "organization_user_student" => [],
                                                            "user" => null,
                                                        ],
                                                        null,
                                                    ],
                                                ],
                                                "organization" => null,
                                                "organization_user_student" => [],
                                                "user" => null,
                                            ],
                                        ],
                                        "type_organizations" => null,
                                    ],
                                    "transaction" => null,
                                    "user" => null,
                                ],
                                "color" => "#FFFFFF",
                            ],
                        ],
                    ],
                    "color" => "#FFFFFF",
                ],
                [
                    "id" => 0,
                    "quantity" => 1,
                    "order_id" => 1,
                    "dish_id" => 3,
                    "dish" => [
                        "id" => 3,
                        "name" => "Nasi Ambeng",
                        "price" => 8.5,
                        "dish_image" =>
                            "https://upload.wikimedia.org/wikipedia/commons/4/44/Nasi_Ambeng.jpg",
                        "created_at" => "2022-07-28T06:53:32+00:00",
                        "updated_at" => "2022-07-28T06:53:34+00:00",
                        "organ_id" => 4,
                        "dish_type" => 9,
                        "dish_available" => [
                            [
                                "id" => 3,
                                "date" => "2022-09-28T00:00:00",
                                "time" => "12:00:00",
                                "latitude" => 2.3138,
                                "longtitude" => 102.3211,
                                "delivery_address" => "Ficts, UTeM, Ayer Keroh",
                                "dish_id" => 3,
                                "dish" => null,
                                "orders" => [null],
                            ],
                        ],
                        "dish_type1" => null,
                        "organization" => [
                            "id" => 4,
                            "code" => "KM001",
                            "email" => "admin_makan@gmail.com",
                            "nama" => "makan-makan",
                            "telno" => "0149392256",
                            "address" => "UTeM, Ayer Keroh",
                            "rating" => "No review yet",
                            "postcode" => "34400",
                            "state" => "Melaka",
                            "fixed_charges" => "2.50",
                            "remember_token" => null,
                            "created_at" => null,
                            "updated_at" => null,
                            "type_org" => 8,
                            "deleted_at" => null,
                            "seller_id" => null,
                            "district" => "(NULL)",
                            "description" => "(NULL)",
                            "city" => null,
                            "organization_picture" =>
                                "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                            "activities" => [],
                            "class_organization" => [],
                            "donation_organization" => [],
                            "fees_new" => [],
                            "organization_user" => [
                                [
                                    "id" => 6,
                                    "organization_id" => 4,
                                    "user_id" => 3,
                                    "role_id" => 2,
                                    "start_date" => null,
                                    "end_date" => null,
                                    "status" => null,
                                    "fees_status" => null,
                                    "class_organization" => [],
                                    "fees_new_organization_user" => [],
                                    "organization_roles" => [
                                        "id" => 2,
                                        "nama" => "Admin",
                                        "organization_user" => [
                                            [
                                                "id" => 1,
                                                "organization_id" => 1,
                                                "user_id" => 1,
                                                "role_id" => 2,
                                                "start_date" => null,
                                                "end_date" => null,
                                                "status" => null,
                                                "fees_status" => null,
                                                "class_organization" => [],
                                                "fees_new_organization_user" => [],
                                                "organization" => [
                                                    "id" => 1,
                                                    "code" => "MS001",
                                                    "email" => "admin_masjid@gmail.com",
                                                    "nama" => "Masjid Al-Alami",
                                                    "telno" => "01139893143",
                                                    "address" => "UTeM, Ayer Keroh",
                                                    "rating" => "No review yet",
                                                    "postcode" => "34400",
                                                    "state" => "Melaka",
                                                    "fixed_charges" => "3.00",
                                                    "remember_token" => null,
                                                    "created_at" =>
                                                        "2020-06-07T02:48:33+00:00",
                                                    "updated_at" =>
                                                        "2020-06-07T02:52:01+00:00",
                                                    "type_org" => 4,
                                                    "deleted_at" => null,
                                                    "seller_id" => null,
                                                    "district" => "",
                                                    "description" => "",
                                                    "city" => null,
                                                    "organization_picture" => null,
                                                    "activities" => [],
                                                    "class_organization" => [],
                                                    "donation_organization" => [
                                                        [
                                                            "id" => 1,
                                                            "donation_id" => 1,
                                                            "organization_id" => 1,
                                                            "donation" => [
                                                                "id" => 1,
                                                                "nama" =>
                                                                    "Derma Kilat Pembinaan Tandas",
                                                                "description" =>
                                                                    "Ayuh Derma",
                                                                "imagebyte" => null,
                                                                "url" =>
                                                                    "Derma-Kilat-Pembinaan-Tandas",
                                                                "date_created" =>
                                                                    "2022-08-26T00:00:00",
                                                                "date_started" =>
                                                                    "2022-08-26T00:00:00",
                                                                "date_end" =>
                                                                    "2022-08-26T00:00:00",
                                                                "status" => "1",
                                                                "tax_payer" => "UTeM",
                                                                "total_tax" => 1,
                                                                "donation_poster" => null,
                                                                "deleted_at" => null,
                                                                "donation_type" => 5,
                                                                "code" => null,
                                                            ],
                                                            "organization" => null,
                                                        ],
                                                        null,
                                                    ],
                                                    "fees_new" => [],
                                                    "organization_user" => [],
                                                    "type_organizations" => null,
                                                ],
                                                "organization_user_student" => [],
                                                "user" => null,
                                            ],
                                            null,
                                        ],
                                    ],
                                    "organization" => null,
                                    "organization_user_student" => [],
                                    "user" => null,
                                ],
                            ],
                            "type_organizations" => null,
                        ],
                        "order_dish" => [null],
                    ],
                    "color" => "#FFFFFF",
                ],
            ],
            "organization" => [
                "id" => 4,
                "code" => "KM001",
                "email" => "admin_makan@gmail.com",
                "nama" => "makan-makan",
                "telno" => "0149392256",
                "address" => "UTeM, Ayer Keroh",
                "rating" => "No review yet",
                "postcode" => "34400",
                "state" => "Melaka",
                "fixed_charges" => "2.50",
                "remember_token" => null,
                "created_at" => null,
                "updated_at" => null,
                "type_org" => 8,
                "deleted_at" => null,
                "seller_id" => null,
                "district" => "(NULL)",
                "description" => "(NULL)",
                "city" => null,
                "organization_picture" =>
                    "https://www.rasa.my/wp-content/uploads/2020/07/FB_IMG_1595315164680.jpg",
                "activities" => [],
                "class_organization" => [],
                "donation_organization" => [],
                "fees_new" => [],
                "organization_user" => [
                    [
                        "id" => 6,
                        "organization_id" => 4,
                        "user_id" => 3,
                        "role_id" => 2,
                        "start_date" => null,
                        "end_date" => null,
                        "status" => null,
                        "fees_status" => null,
                        "class_organization" => [],
                        "fees_new_organization_user" => [],
                        "organization_roles" => [
                            "id" => 2,
                            "nama" => "Admin",
                            "organization_user" => [
                                [
                                    "id" => 1,
                                    "organization_id" => 1,
                                    "user_id" => 1,
                                    "role_id" => 2,
                                    "start_date" => null,
                                    "end_date" => null,
                                    "status" => null,
                                    "fees_status" => null,
                                    "class_organization" => [],
                                    "fees_new_organization_user" => [],
                                    "organization" => [
                                        "id" => 1,
                                        "code" => "MS001",
                                        "email" => "admin_masjid@gmail.com",
                                        "nama" => "Masjid Al-Alami",
                                        "telno" => "01139893143",
                                        "address" => "UTeM, Ayer Keroh",
                                        "rating" => "No review yet",
                                        "postcode" => "34400",
                                        "state" => "Melaka",
                                        "fixed_charges" => "3.00",
                                        "remember_token" => null,
                                        "created_at" => "2020-06-07T02:48:33+00:00",
                                        "updated_at" => "2020-06-07T02:52:01+00:00",
                                        "type_org" => 4,
                                        "deleted_at" => null,
                                        "seller_id" => null,
                                        "district" => "",
                                        "description" => "",
                                        "city" => null,
                                        "organization_picture" => null,
                                        "activities" => [],
                                        "class_organization" => [],
                                        "donation_organization" => [
                                            [
                                                "id" => 1,
                                                "donation_id" => 1,
                                                "organization_id" => 1,
                                                "donation" => [
                                                    "id" => 1,
                                                    "nama" =>
                                                        "Derma Kilat Pembinaan Tandas",
                                                    "description" => "Ayuh Derma",
                                                    "imagebyte" => null,
                                                    "url" =>
                                                        "Derma-Kilat-Pembinaan-Tandas",
                                                    "date_created" =>
                                                        "2022-08-26T00:00:00",
                                                    "date_started" =>
                                                        "2022-08-26T00:00:00",
                                                    "date_end" => "2022-08-26T00:00:00",
                                                    "status" => "1",
                                                    "tax_payer" => "UTeM",
                                                    "total_tax" => 1,
                                                    "donation_poster" => null,
                                                    "deleted_at" => null,
                                                    "donation_type" => 5,
                                                    "code" => null,
                                                ],
                                                "organization" => null,
                                            ],
                                            null,
                                        ],
                                        "fees_new" => [],
                                        "organization_user" => [],
                                        "type_organizations" => null,
                                    ],
                                    "organization_user_student" => [],
                                    "user" => null,
                                ],
                                null,
                            ],
                        ],
                        "organization" => null,
                        "organization_user_student" => [],
                        "user" => null,
                    ],
                ],
                "type_organizations" => null,
            ],
            "transaction" => null,
            "user" => null,
        ];

        try {

            $this->validate($request, [
                'name'          =>  'required',
                'email'         =>  'required',
                'telno'         =>  'required',
                'organization'  =>  'required',
            ]);

            $order = new Order();
            $order->name = $re
            
        } catch (\Throwable $th) {

        }
    }
}
