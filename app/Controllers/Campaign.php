<?php

namespace App\Controllers;

use App\Models\CampaignModel;

class Campaign extends BaseController
{
    protected CampaignModel $campaignModel;

    public function __construct()
    {
        $this->campaignModel = new CampaignModel();
    }

    public function index()
    {
        return view('backoffice/campaign');
    }

    public function get_campaign()
    {
        $campaign = $this->campaignModel
            ->select('
                campaign.*,
                product_category.name AS category_name
            ')
            ->join('product_category', 'product_category.id=campaign.category_id', 'left')
            ->findAll();
        $data = [];
        foreach ($campaign as $i => $row) {
            $type_badge = '';
            $subtype = '';
            if($row["type"] == 'Coupon'){
                $type_badge = '<span class="badge bg-light-success">Coupon</span>';
            }elseif ($row["type"] == 'On Top') {
                $type_badge = '<span class="badge bg-light-info">On Top</span>';
            }elseif ($row["type"] == 'Seasonal') {
                $type_badge = '<span class="badge bg-light-warning">Seasonal</span>';
            }

            if($row["subtype"] == 'FIXED_AMOUNT'){
                $subtype = 'Fixed Amount';
            }elseif ($row["subtype"] == 'PERCENTAGE') {
                $subtype = 'Percentage Discount';
            }elseif ($row["subtype"] == 'CATEGORY_PERCENTAGE') {
                $subtype = 'Discount by Category';
            }elseif ($row["subtype"] == 'POINTS') {
                $subtype = 'Discount by Point';
            }elseif ($row["subtype"] == 'THRESHOLD_FIXED') {
                $subtype = 'Threshold Amount';
            }


            $data[] = [
                'no' => ++$i,
                'name' => $row["name"],
                'type' => $type_badge,
                'subtype' => $subtype,
                'amount' => $row["amount"],
                'percentage' => $row["percentage"],
                'category_id' => $row["category_name"],
                'threshold_amount' => $row["threshold_amount"],
                'max_discount_percentage' => $row["max_discount_percentage"],
                'points_per_currency' => $row["points_per_currency"],
                'action' => '<button type="button"
                    class="btn btn-sm btn-warning" 
                    data-id="' . esc($row['id'], 'attr') . '" disabled>
                    <i class="fa fa-edit"></i> Edit
                </button> <button type="button" 
                    class="btn btn-sm btn-danger btn-delete-campaign"
                    data-id="' . esc($row['id'], 'attr') . '">
                    <i class="fa fa-trash"></i> Delete
                </button>'
            ];
        }

        return $this->response->setJSON([
            'data'            => $data
        ]);
    }

    public function add_campaign()
    {
        debug('add_campaign');
        $request = $this->request->getPost();

        $dataCampaign = [];

        $type = $request["type"];
        $sub_type = $request["sub_type"];

        //Set Data Campaign
        //FIXED_AMOUNT
        if ($sub_type == 'FIXED_AMOUNT') {
            $dataCampaign = [
                "name" => $request["name"],
                "type" => $type,
                "subtype" => $sub_type,
                "amount" => $request["amount"] ?? 0
            ];
        } elseif ($sub_type == 'PERCENTAGE') {
            //PERCENTAGE
            $dataCampaign = [
                "name" => $request["name"],
                "type" => $type,
                "subtype" => $sub_type,
                "percentage" => $request["percentage"] ?? 0
            ];
        } elseif ($sub_type == 'CATEGORY_PERCENTAGE') {
            //CATEGORY_PERCENTAGE
            $dataCampaign = [
                "name" => $request["name"],
                "type" => $type,
                "subtype" => $sub_type,
                "percentage" => $request["category_percentage"] ?? 0,
                "category_id" => $request["p_category"]
            ];
        } elseif ($sub_type == 'POINTS') {
            // POINTS
            $dataCampaign = [
                "name" => $request["name"],
                "type" => $type,
                "subtype" => $sub_type,
                "points_per_currency" => $request["points_per_currency"]
            ];
        } elseif ($sub_type == 'THRESHOLD_FIXED') {
            $dataCampaign = [
                "name" => $request["name"],
                "type" => $type,
                "subtype" => $sub_type,
                "amount" => $request["discount_amount"] ?? 0,
                "threshold_amount" => $request["threshold_amount"] ?? 0,
            ];
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save. Please try again.',
                'errors'  => null
            ]);
        }

        //Insert
        try {
            $insert = $this->campaignModel->save($dataCampaign);
            if ($insert === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save. Please try again.',
                    'errors'  => $this->campaignModel->errors(),
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

    public function delete_campaign()
    {
        $request = $this->request->getPost();
        $id = $request["id"];

        try {
            $delete = $this->campaignModel->delete($id);

            if ($delete === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete. Please try again.',
                    'errors'  => $this->campaignModel->errors(),
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
