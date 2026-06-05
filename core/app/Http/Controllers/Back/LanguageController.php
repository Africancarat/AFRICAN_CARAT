<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LanguageController extends Controller
{
    /**
     * Constructor Method.
     *
     * Setting Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index()
    {
        $datas = Language::get();
        return view('back.language.index', compact('datas'));
    }

    public function create()
    {
        $data = Language::first();
        $data_results = file_get_contents(resource_path() . '/lang/' . $data->file);
        $lang = json_decode($data_results, true);

        return view('back.language.create', compact('data', 'lang'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'language' => 'required|unique:languages,language',
        ]);
        $new = null;
        $input = $request->all();
        $data = new Language();

        $name = time() . Str::random(8);
        $data->name = Str::random(8);
        $data->language = $request->language;
        $data->file = $name . '.json';
        $data->type = "Website";
        $data->save();

        $language = Language::findOrFail(1)->file;
        $string = file_get_contents(resource_path() . '/lang/' . $language);
        $languages = json_decode($string, true);

        foreach ($languages as $key => $value) {
            $n = str_replace("_", " ", $key);
            $new[$n] = $value;
        }
        $mydata = json_encode($new);
        file_put_contents(resource_path() . '/lang/' . $data->file, $mydata);
        return redirect()->route('back.language.index')->withSuccess(__('Language Added Successfully.'));
    }

    public function edit($id)
    {
        $data = Language::find($id);
        $data_results = file_get_contents(resource_path() . '/lang/' . $data->file);
        $lang = json_decode($data_results, true);
        return view('back.language.edit', compact('data', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //--- Logic Section
        $new = null;
        $data = Language::find($id);
        if (file_exists(resource_path() . '/lang/' . $data->file)) {
            unlink(resource_path() . '/lang/' . $data->file);
        }
        $name = time() . Str::random(8);
        $data->name = $name;
        $data->language = $request->language;
        $data->file = $name . '.json';
        $data->update();

        $keys = $request->keys;
        $values = $request->values;
        foreach (array_combine($keys, $values) as $key => $value) {
            $n = str_replace("_", " ", $key);
            $new[$n] = $value;
        }
        $mydata = json_encode($new);
        file_put_contents(resource_path() . '/lang/' . $data->file, $mydata);
        //--- Logic Section Ends

        return redirect()->back()->withSuccess(__('Language Updated Successfully.'));
    }

    public function status($id, $status)
    {
        $data = Language::findOrFail($id);
        $type = $data->type;
        $get = Language::whereType($type)->where('id', "!=", $id)->get();
        $data = Language::findOrFail($id);
        $data->is_default = $status;
        
        if ($status == 1) {
            foreach ($get as $lang) {
                $lang->is_default = 0;
                $lang->update();
            }
        }

        $data->update();
        return redirect()->route('back.language.index')->withSuccess(__('Language Updated Successfully.'));
    }

    public function destroy($id)
    {
        $data = Language::find($id);
        if($data->is_default == 1 || $data->id == 1){
            return redirect()->back()->withSuccess(__("You can't delete this language"));
        }
        
        if (file_exists(resource_path() . '/lang/' . $data->file)) {
            unlink(resource_path() . '/lang/' . $data->file);
        }
        $data->delete();
        return redirect()->back()->withSuccess(__('Language Delete Successfully.'));
    }

}
