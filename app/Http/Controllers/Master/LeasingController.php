<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Leasing;


class LeasingController extends Controller
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
        $this->model = new Leasing();
        $this->module = 'master.leasing';
        $this->page = 'leasing';
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
            'result' => $this->model->all(),
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
            'page' => $this->page
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
            'name'     => 'required',
            'admin_cost'     => 'required'
        ]);

        $create = [
            'name'  => $request->input('name'),
            'contact_name'  => $request->input('contact_name'),
            'admin_cost'  => parseMoneyToInteger($request->input('admin_cost')),
            'phone'  => $request->input('phone'),
            'areas' => '',
            'fax'  => $request->input('fax'),
            'email'  => $request->input('email'),
            'address'  => $request->input('address'),
            'created_by' => Auth::id()
        ];

        $this->model->create($create);

        logUser('Create Master Leasing '.$create['name']);

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
            'row' => $this->model->find($id)
        ];

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
            'name'     => 'required',
            'admin_cost'     => 'required'
        ]);

        $data = $this->model->find($id);

        $update = [
            'name'  => $request->input('name'),
            'contact_name'  => $request->input('contact_name'),
            'phone'  => $request->input('phone'),
            'fax'  => $request->input('fax'),
            'areas' => '',
            'admin_cost'  => parseMoneyToInteger($request->input('admin_cost')),
            'email'  => $request->input('email'),
            'address'  => $request->input('address'),
            'updated_by' => Auth::id()
        ];

        $data->update($update);

        logUser('Update Master Leasing '.$update['name']);

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
        $data = $this->model->find($id);
        $message = setDisplayMessage('success', "Success to delete ".$this->page);
        logUser('Delete Master Leasing '.$data->name);
        $data->delete();
        return redirect(route($this->page.'.index'))->with('displayMessage', $message);
    }

    /**
     * @param $id
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id, $status) {
        $data = $this->model->find($id);

        $data->status = $status;

        $desc = ($status == 1) ? 'activate' : 'deactivate';

        $data->save();

        logUser('Change Status Master Leasing '.$data->name);

        $message = setDisplayMessage('success', "Success to $desc ".$this->page);
        return redirect(route($this->page.'.index'))->with('displayMessage', $message);
    }
}
