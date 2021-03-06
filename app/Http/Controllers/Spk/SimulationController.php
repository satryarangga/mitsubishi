<?php

namespace App\Http\Controllers\Spk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Simulation;
use App\Models\CreditMonth;
use App\Models\Leasing;
use App\Models\CarModel;
use App\Models\CarType;

class SimulationController extends Controller
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $page;

    /**
     * @var string
     */
    private $model;


    public function __construct() {
        $this->model = new Simulation();
        $this->module = 'spk.simulation';
        $this->page = 'simulation';
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'result' => $this->model->where('created_by', Auth::id())->get(),
            'page' => $this->page
        ];
        return view($this->module . ".index", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'page' => $this->page,
            'leasing' => Leasing::all(),
            'carType' => CarType::all(),
            'months' => CreditMonth::all()
        ];

        return view($this->module.".create", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'leasing_id'     => 'required',
            'total_sales_price'     => 'required',
            'duration'     => 'required',
            'dp_amount'     => 'required',
            'dp_percentage'     => 'required'
        ]);

        $create = [
            'leasing_id'  => $request->input('leasing_id'),
            'car_category_id'  => 0,
            'car_model_id'  => 0,
            'customer_name'  => $request->input('customer_name'),
            'car_type_id'   => 0,
            'car_year'  => 2017,
            'price'  => parseMoneyToInteger($request->input('total_sales_price')),
            'dp_amount'  => parseMoneyToInteger($request->input('dp_amount')),
            'dp_percentage'  => $request->input('dp_percentage'),
            'duration'  => $request->input('duration'),
            'admin_cost'  => parseMoneyToInteger($request->input('admin_cost')),
            'installment_cost'  => parseMoneyToInteger($request->input('installment_cost')),
            'interest_rate'  => $request->input('interest_rate'),
            'insurance_cost'  => parseMoneyToInteger($request->input('insurance_cost')),
            'other_cost'  => parseMoneyToInteger($request->input('other_cost')),
            'total_dp'  => parseMoneyToInteger($request->input('total_dp')),
            'created_by' => Auth::id(),
            'uuid'      => uuidV4()
        ];

        $head = $this->model->create($create);

        logUser('Create Simulation '.$head->id);

        $message = setDisplayMessage('success', "Success to create new ".$this->page);
        return redirect(route($this->page.'.index'))->with('displayMessage', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'page' => $this->page,
            'row' => $this->model->find($id),
            'leasing' => Leasing::all(),
            'carType' => CarType::all(),
            'months' => CreditMonth::all(),
        ];

        $data['totalInterest'] = $this->model->totalInterest($data['row']->installment_cost, $data['row']->duration, $data['row']->price, $data['row']->dp_amount);
        $data['totalPayment'] = $data['totalInterest'] + ($data['row']->price - $data['row']->dp_amount);

        return view($this->module.".edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'leasing_id'     => 'required',
            'total_sales_price'     => 'required',
            'dp_amount'     => 'required',
            'duration'     => 'required',
            'dp_percentage'     => 'required'
        ]);

        $data = $this->model->find($id);

        $update = [
            'leasing_id'  => $request->input('leasing_id'),
            'car_category_id'  => 0,
            'car_model_id'  => 0,
            'customer_name'  => $request->input('customer_name'),
            'car_type_id'   => 0,
            'car_year'  => 2017,
            'price'  => parseMoneyToInteger($request->input('total_sales_price')),
            'dp_amount'  => parseMoneyToInteger($request->input('dp_amount')),
            'dp_percentage'  => $request->input('dp_percentage'),
            'duration'  => $request->input('duration'),
            'admin_cost'  => parseMoneyToInteger($request->input('admin_cost')),
            'installment_cost'  => parseMoneyToInteger($request->input('installment_cost')),
            'interest_rate'  => $request->input('interest_rate'),
            'insurance_cost'  => parseMoneyToInteger($request->input('insurance_cost')),
            'other_cost'  => parseMoneyToInteger($request->input('other_cost')),
            'total_dp'  => parseMoneyToInteger($request->input('total_dp')),
            'updated_by' => Auth::id()
        ];

        $data->update($update);

        logUser('Update Simulation '.$id);

        $message = setDisplayMessage('success', "Success to update ".$this->page);
        return redirect(route($this->page.'.index'))->with('displayMessage', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->model->find($id)->delete();
        $message = setDisplayMessage('success', "Success to delete ".$this->page);
        logUser('Delete Simulation '.$id);
        return redirect(route($this->page.'.index'))->with('displayMessage', $message);
    }
}
