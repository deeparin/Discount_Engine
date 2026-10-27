<?php

namespace App\Controllers;

use App\Models\CampaignModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductsModel;
use Aws\S3\S3Client;

class Backoffice extends BaseController
{
    //Model
    protected $productCategoryModel;
    protected $productsModel;
    protected $campaignModel;

    //S3
    private $s3;
    private $bucket;

    public function __construct()
    {
        $this->productCategoryModel = new ProductCategoryModel();
        $this->productsModel = new ProductsModel();
        $this->campaignModel = new CampaignModel();

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
    public function index()
    {
        $data = [
            'totalProducts'   => $this->productsModel->countAllResults(),
            'totalCategories' => $this->productCategoryModel->countAllResults(),
            'totalCampaigns'  => $this->campaignModel->countAllResults(),
            'activeCampaigns' => $this->campaignModel->countAllResults(),
        ];

        return view('backoffice/index', $data);
    }

    public function products()
    {
        return view('backoffice/products');
    }

    public function category()
    {
        return view('backoffice/category');
    }

    public function get_category()
    {
        $category = $this->productCategoryModel->findAll();
        $data = [];
        foreach ($category as $i => $row) {
            $data[] = [
                'no' => ++$i,
                'name' => $row["name"],
                'action' => '<button type="button"
                    class="btn btn-sm btn-warning btn-edit-category" 
                    data-bs-toggle="modal"
                    data-bs-target="#edit_category" data-id="' . esc($row['id'], 'attr') . '"
                    data-name="' . esc($row['name'], 'attr') . '">
                    <i class="fa fa-edit"></i> Edit
                </button> <button type="button" 
                    class="btn btn-sm btn-danger btn-delete-category"
                    data-id="' . esc($row['id'], 'attr') . '">
                    <i class="fa fa-trash"></i> Delete
                </button>'
            ];
        }

        return $this->response->setJSON([
            'data'            => $data
        ]);
    }

    public function add_category()
    {
        $request = $this->request->getPost();
        $name = trim((string) ($request['name'] ?? ''));
        $name = preg_replace('/[^\p{L}\p{N}\s\-_&().,\']/u', '', $name);

        try {
            $insert = $this->productCategoryModel->insert([
                "name" => $name
            ]);
            if ($insert === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save. Please try again.',
                    'errors'  => $this->productCategoryModel->errors(),
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Saved successfully.',
                'errors'  => null,
            ]);
        } catch (\Throwable $e) {
            debug('error ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'errors'  => $e->getMessage(),
            ]);
        }
    }

    public function update_category()
    {
        $request = $this->request->getPost();

        $id = $request["id"];
        $name = trim((string) $request["name"]);

        try {
            $update = $this->productCategoryModel->update($id, [
                "name" => $name
            ]);

            if ($update === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save. Please try again.',
                    'errors'  => $this->productCategoryModel->errors(),
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Saved successfully.',
                'errors'  => null,
            ]);
        } catch (\Throwable $e) {
            debug('error ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'errors'  => $e->getMessage(),
            ]);
        }
    }

    public function delete_category()
    {
        $request = $this->request->getPost();
        $id = $request["id"];

        try {
            $delete = $this->productCategoryModel->delete($id);

            if ($delete === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete. Please try again.',
                    'errors'  => $this->productCategoryModel->errors(),
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Item deleted successfully.',
                'errors'  => null,
            ]);
        } catch (\Throwable $e) {
            debug('error ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'errors'  => $e->getMessage(),
            ]);
        }
    }

    public function get_products()
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
            if (! empty($row['image'])) {
                $cmd = $this->s3->getCommand('GetObject', [
                    'Bucket' => $this->bucket,
                    'Key'    => $row['image'],
                ]);
                $img = (string) $this->s3->createPresignedRequest($cmd, '+60 minutes')->getUri();
            } else {
                $img = base_url('assets/images/unity/no_image_1-1.jpeg');
            }

            $data[] = [
                'no' => ++$i,
                'name' => $row["name"],
                'image' => '<div class="mx-auto" style="width: 50px; height: 50px;">
                    <img src="' . esc($img, 'attr') . '"
                        class="object-fit-cover w-100 h-100 rounded"
                        alt="Store Image">
                </div>',
                'category' => $row['category_name'],
                'price' => number_format((float) $row["price"], 2),
                'action' => '<button type="button"
                    class="btn btn-sm btn-warning btn-edit-product" 
                    data-id="' . esc($row['id'], 'attr') . '">
                    <i class="fa fa-edit"></i> Edit
                </button> <button type="button" 
                    class="btn btn-sm btn-danger btn-delete-product"
                    data-id="' . esc($row['id'], 'attr') . '">
                    <i class="fa fa-trash"></i> Delete
                </button>'
            ];
        }

        return $this->response->setJSON([
            'data'            => $data
        ]);
    }

    public function dropdown_get_category()
    {
        $category = $this->productCategoryModel->findAll();
        return $this->response->setJSON($category);
    }

    public function add_product()
    {
        $request = $this->request->getPost();
        $file = $this->request->getFile("p_image");

        $name = trim((string) ($request['p_name'] ?? ''));
        $name = preg_replace('/[^\p{L}\p{N}\s\-_&().,\']/u', '', $name);

        try {
            $imageUrl = null;
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $imageUrl = $this->uploadToMinio($file);
            }
            $insert = $this->productsModel->insert([
                "name" => $name,
                "image" => $imageUrl,
                "category_id" => $request["p_category"],
                "price" => $request["p_price"]
            ]);
            if ($insert === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save. Please try again.',
                    'errors'  => $this->productCategoryModel->errors(),
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Saved successfully.',
                'errors'  => null,
            ]);
        } catch (\Throwable $e) {
            debug('error ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'errors'  => $e->getMessage(),
            ]);
        }
    }

    private function uploadToMinio($file): string
    {

        $extension = $file->getClientExtension();
        $newName   = 'products/' .  uniqid() . '_' . time() . '.' . $extension;


        $tempPath = WRITEPATH . 'uploads/' . uniqid() . '_resized.' . $extension;


        if (!is_dir(WRITEPATH . 'uploads')) {
            mkdir(WRITEPATH . 'uploads', 0755, true);
        }

        \Config\Services::image()
            ->withFile($file->getTempName())
            ->fit(200, 200, 'center')
            ->save($tempPath);

        $this->s3->putObject([
            'Bucket'      => $this->bucket,
            'Key'         => $newName,
            'SourceFile'  => $tempPath,
            'ContentType' => $file->getMimeType(),
            'ACL'         => 'public-read',
        ]);

        // ลบไฟล์ temp ทิ้งหลังอัปโหลดเสร็จ
        unlink($tempPath);

        // Return URL สำหรับเข้าถึงไฟล์
        return $newName;
    }

    public function get_product($id)
    {
        // debug('get_product '.$id);
        $product = $this->productsModel->find($id);

        if (! $product) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'ไม่พบข้อมูล',
            ]);
        }
        debug($product);

        if ($product["image"]) {
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $product['image'],
            ]);

            $img = (string) $this->s3->createPresignedRequest($cmd, '+60 minutes')->getUri();
            $product["show_image"] = $img;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $product,
        ]);
    }

    public function update_product()
    {
        $request = $this->request->getPost();
        $file = $this->request->getFile("p_edit_image");

        $name = trim((string) ($request['p_edit_name'] ?? ''));
        $name = preg_replace('/[^\p{L}\p{N}\s\-_&().,\']/u', '', $name);

        $product_data = [
            "name" => $name,
            "category_id" => $request["p_edit_category"],
            "price" => $request["p_edit_price"],
        ];

        try {
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $imageUrl = $this->uploadToMinio($file);
                $product_data['image'] = $imageUrl;

                // ลบไฟล์เก่า ถ้ามี
                $oldImg = $request["p_edit_oldimg"];
                if (!empty($oldImg)) {
                    $this->deleteFromMinio($oldImg);
                }
            }

            $update = $this->productsModel->update($request["p_edit_id"], $product_data);

            if ($update === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save. Please try again.',
                    'errors'  => $this->productCategoryModel->errors(),
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Saved successfully.',
                'errors'  => null,
            ]);
        } catch (\Throwable $e) {
            debug('error ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'errors'  => $e->getMessage(),
            ]);
        }
    }

    private function deleteFromMinio(string $key): void
    {
        $key = trim($key, '/'); // กันเผื่อมี / นำหน้า/ท้ายเกินมา

        if (empty($key)) {
            return;
        }

        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'ลบไฟล์เก่าจาก MinIO ไม่สำเร็จ: ' . $e->getMessage());
        }
    }

    public function delete_product()
    {
        $request = $this->request->getPost();
        $id = $request["id"];

        try {
            $delete = $this->productsModel->delete($id);

            if ($delete === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete. Please try again.',
                    'errors'  => $this->productsModel->errors(),
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Item deleted successfully.',
                'errors'  => null,
            ]);
        } catch (\Throwable $e) {
            debug('error ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'errors'  => $e->getMessage(),
            ]);
        }
    }
}
