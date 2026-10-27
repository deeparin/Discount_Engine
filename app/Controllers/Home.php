<?php

namespace App\Controllers;

use App\Models\ProductCategoryModel;
use App\Models\ProductsModel;
use Aws\S3\S3Client;

class Home extends BaseController
{

    //Model
    protected $productCategoryModel;
    protected $productsModel;

    //S3
    private $s3;
    private $bucket;

    public function __construct()
    {
        $this->productCategoryModel = new ProductCategoryModel();
        $this->productsModel = new ProductsModel();

        //S3
        $this->s3 = new S3Client([
            'version'                 => 'latest',
            'region'                  => 'us-east-1',
            'endpoint'                => env('S3_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => env('S3_KEY'),
                'secret' => env('S3_SECRET'),
            ],
        ]);
        $this->bucket = env('S3_BUCKET');
    }

    public function index(): string
    {
        $products = $this->productsModel
            ->select('
                products.*,
                product_category.name AS category_name,
            ')
            ->join('product_category', 'product_category.id=products.category_id', 'left')
            ->findAll();


        $data = [];

        foreach ($products as $i => $row) {

            if (!empty($row['image'])) {
                $cmd = $this->s3->getCommand('GetObject', [
                    'Bucket' => $this->bucket,
                    'Key'    => $row['image'],
                ]);

                $img = (string) $this->s3
                    ->createPresignedRequest($cmd, '+60 minutes')
                    ->getUri();
            } else {
                $img = base_url('assets/images/unity/no_image_1-1.jpeg');
            }

            // เพิ่ม URL รูปภาพเข้าไปในข้อมูลสินค้า
            $row['image_url'] = $img;

            $data[] = $row;
        }

        return view('frontweb/index', [
            'products' => $data
        ]);
    }
}
