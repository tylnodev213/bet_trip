<?php

namespace App\Models;

use App\Libraries\Utilities;
use App\Traits\GetListData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class Coupon extends Model
{
    use HasFactory, GetListData;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = "coupons";
    protected $guarded = [];
    protected $path = 'public/images/coupons/';

    /**
     * Validate rules for coupon
     *
     * @return string[]
     */
    public function rules(int $id = null): array
    {
        $rule = [
            'code' => ['required','string','between:5,20', !empty($id) ? Rule::unique(Coupon::class)->ignore($id) : Rule::unique(Coupon::class)],
            'discount' => 'required|integer|between:1,100',
            'number' => 'required|integer|between:0,1000000',
            'status' => 'required|integer|between:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'banner' => !empty($id) ? 'image|mimes:jpeg,jpg,png,gif|max:5000' : 'required|image|mimes:jpeg,jpg,png,gif|max:5000',
        ];

        return $rule;
    }

    /**
     * Save coupon
     *
     * @param Request $request
     * @param int $id
     * @return int
     */
    public function saveData(Request $request, int $id = 0)
    {
        $input = $request->only('code', 'discount', 'number', 'status', 'start_date', 'end_date');
        $input = Utilities::clearAllXSS($input);
        $coupon = $this->findOrNew($id);
        $oldBanner = $coupon->banner;

        if ($request->hasFile('banner')) {
            $input['banner'] = Utilities::storeImage($request->file('banner'), $this->path);
        }

        $coupon->fill($input);

        if ($coupon->save()) {
            if ($request->hasFile('banner')) {
                Storage::delete($this->path . $oldBanner);
            }
        } else {
            Storage::delete($this->path . $coupon->image);
        }

        return 1;
    }

    /**
     * Delete the coupon by id in database.
     *
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        $coupon = $this->findOrFail($id);
        Storage::delete($this->path . $coupon->banner);
        return $coupon->delete();
    }

    /**
     * Get a list of coupon
     *
     * @param $tourId
     * @return mixed
     * @throws \Exception
     */
    public function getList($request)
    {
        $search = $request->search;
        $status = $request->status;
        $query = $this->latest();

        if (!empty($search)) {
            $query->where('code', 'like', '%' . $search . '%');
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->setRowId(function ($data) {
                return 'coupon-' . $data->id;
            })
            ->editColumn('start_date', function ($data) {
                return Carbon::parse($data->start_date)->format('Y-m-d H:i');
            })
            ->editColumn('end_date', function ($data) {
                return Carbon::parse($data->end_date)->format('Y-m-d H:i');
            })
            ->editColumn('status', function ($data) {
                $link = route('coupons.update', $data->id);
                return view('components.button_switch', ['status' => $data->status, 'link' => $link]);
            })
            ->editColumn('banner', function ($data) {
                $pathImage = asset("storage/images/coupons/" . $data->banner);
                return view('components.image', compact('pathImage'));
            })
            ->addColumn('action', function ($data) {
                $id = $data->id;
                $linkEdit = route("coupons.update", [$data->id]);
                $linkDelete = route("coupons.destroy", [$data->id]);

                return view('components.action_modal', compact(['id', 'linkEdit', 'linkDelete']));
            })
            ->rawColumns(['banner', 'status', 'action'])
            ->make(true);
    }
}
